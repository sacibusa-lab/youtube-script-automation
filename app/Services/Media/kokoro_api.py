import os
import sys
import json
import io
import wave
import numpy as np
import soundfile as sf
from fastapi import FastAPI, Request, Response
from fastapi.responses import StreamingResponse
from kokoro_onnx import Kokoro
import uvicorn

# Configuration
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "../../../storage/app/models/kokoro/kokoro-v0_19.onnx")
VOICES_PATH = os.path.join(BASE_DIR, "../../../storage/app/models/kokoro/voices.bin")

app = FastAPI(title="Kokoro TTS API Bridge")

# Initialize Kokoro (Load once into memory)
print(f"Loading Kokoro model from {MODEL_PATH}...")
if not os.path.exists(MODEL_PATH):
    print(f"ERROR: Model file not found. Please run setup_kokoro.py first.")
    sys.exit(1)

kokoro = Kokoro(MODEL_PATH, VOICES_PATH)

@app.post("/v1/audio/speech")
async def generate_speech(request: Request):
    try:
        data = await request.json()
        text = data.get("input", "")
        voice = data.get("voice", "af_heart")
        speed = float(data.get("speed", 1.0))
        
        if not text:
            return {"error": "Missing input text"}, 400

        print(f"Synthesizing: '{text[:50]}...' with voice '{voice}' at speed {speed}")
        
        # Generate audio
        samples, sample_rate = kokoro.create(text, voice=voice, speed=speed)
        
        # Convert to MP3 (via soundfile)
        buffer = io.BytesIO()
        sf.write(buffer, samples, sample_rate, format='mp3')
        buffer.seek(0)
        
        return Response(content=buffer.read(), media_type="audio/mpeg")
    
    except Exception as e:
        print(f"Error: {e}")
        return {"error": str(e)}, 500

@app.get("/health")
async def health():
    return {"status": "ok", "model_loaded": True}

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 8880))
    print(f"Starting Kokoro API Server on port {port}...")
    uvicorn.run(app, host="0.0.0.0", port=port)
