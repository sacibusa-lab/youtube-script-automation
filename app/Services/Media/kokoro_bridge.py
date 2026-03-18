import sys
import os
import json
import argparse
from kokoro_onnx import Kokoro
import soundfile as sf
import logging

# Suppress debug output from libraries
logging.getLogger().setLevel(logging.ERROR)
for name in logging.root.manager.loggerDict:
    logging.getLogger(name).setLevel(logging.ERROR)

def main():
    parser = argparse.ArgumentParser(description='Kokoro TTS Bridge')
    parser.add_argument('--text', type=str, required=True, help='Text to speak')
    parser.add_argument('--voice', type=str, default='af_heart', help='Voice ID')
    parser.add_argument('--output', type=str, required=True, help='Output MP3/WAV path')
    parser.add_argument('--model', type=str, help='Path to kokoro-v0_19.onnx')
    parser.add_argument('--voices_bin', type=str, help='Path to voices.bin')
    parser.add_argument('--speed', type=float, default=1.0, help='Speed (0.5 - 2.0)')
    parser.add_argument('--volume', type=float, default=1.0, help='Volume (0.0 - 2.0)')
    
    args = parser.parse_args()

    # Paths to model files - we'll expect them in a specific storage location
    # If not provided, the library might try to download or we fail gracefully
    model_path = args.model
    voices_path = args.voices_bin

    if not model_path or not os.path.exists(model_path):
        print(json.dumps({"success": False, "error": f"Model not found at {model_path}"}))
        sys.exit(1)

    try:
        kokoro = Kokoro(model_path, voices_path)
        samples, sample_rate = kokoro.create(
            args.text, 
            voice=args.voice, 
            speed=args.speed, 
            lang="en-us"
        )
        
        # Apply volume scaling
        if args.volume != 1.0:
            samples = samples * args.volume
            
        # Save output
        sf.write(args.output, samples, sample_rate)
        
        print(json.dumps({
            "success": True, 
            "output": args.output,
            "voice": args.voice,
            "sample_rate": sample_rate
        }))

    except Exception as e:
        print(json.dumps({"success": False, "error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    main()
