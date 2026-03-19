import os
import sys
import json
import io
import time
from fastapi import FastAPI, Request, Response
from fastapi.responses import JSONResponse
import uvicorn

# Configuration
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
# Check both relative to script and relative to project root
MODEL_PATH = os.path.abspath(os.path.join(BASE_DIR, "../../../storage/app/models/kokoro/kokoro-v0_19.onnx"))
VOICES_PATH = os.path.abspath(os.path.join(BASE_DIR, "../../../storage/app/models/kokoro/voices.bin"))

app = FastAPI(title="Kokoro TTS API Bridge")

# Global model instance
kokoro = None

def init_model():
    global kokoro
    from kokoro_onnx import Kokoro
    print(f"--- Kokoro API Server Startup ---")
    print(f"Checking Model: {MODEL_PATH}")
    print(f"Checking Voices: {VOICES_PATH}")
    
    if not os.path.exists(MODEL_PATH) or not os.path.exists(VOICES_PATH):
        print(f"ERROR: Model files missing! Please run 'python3 app/Services/Media/setup_kokoro.py' first.")
        return False

    try:
        print("Loading model into memory (this may take 10-20 seconds)...")
        start = time.time()
        kokoro = Kokoro(MODEL_PATH, VOICES_PATH)
        print(f"SUCCESS: Model loaded in {time.time() - start:.2f} seconds.")
        return True
    except Exception as e:
        print(f"CRITICAL ERROR loading model: {e}")
        return False

@app.post("/v1/audio/speech")
async def generate_speech(request: Request):
    if not kokoro:
        return JSONResponse(content={"error": "Model not loaded on server"}, status_code=503)
    
    try:
        data = await request.json()
        text = data.get("input", "")
        voice = data.get("voice", "af_heart")
        speed = float(data.get("speed", 1.0))
        
        if not text:
            return JSONResponse(content={"error": "Missing input text"}, status_code=400)

        print(f"[{time.strftime('%H:%M:%S')}] Synthesizing: '{text[:40]}...' ({voice})")
        
        # Generate audio
        import soundfile as sf
        samples, sample_rate = kokoro.create(text, voice=voice, speed=speed)
        
        # Convert to MP3/WAV
        buffer = io.BytesIO()
        # Fallback to WAV if MP3 support is missing in libsndfile
        try:
            sf.write(buffer, samples, sample_rate, format='mp3')
        except:
            print("Notice: libsndfile too old for MP3, falling back to WAV...")
            sf.write(buffer, samples, sample_rate, format='wav')
            
        buffer.seek(0)
        return Response(content=buffer.read(), media_type="audio/mpeg")
    
    except Exception as e:
        print(f"Generation Error: {e}")
        return JSONResponse(content={"error": str(e)}, status_code=500)

@app.get("/health")
async def health():
    return {"status": "ok", "model_ready": kokoro is not None}

if __name__ == "__main__":
    if init_model():
        port = int(os.environ.get("PORT", 8880))
        print(f"\n🚀 SERVER IS READY AND LISTENING ON PORT {port}")
        print(f"Test with: curl http://127.0.0.1:{port}/health")
        uvicorn.run(app, host="127.0.0.1", port=port, log_level="warning")
    else:
        print("\n❌ FAILED TO START: Model initialization aborted.")
        sys.exit(1)
