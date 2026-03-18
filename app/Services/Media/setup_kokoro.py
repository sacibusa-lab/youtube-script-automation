import os
import sys
import subprocess
import urllib.request

def install_deps():
    print("--- Installing Python Dependencies ---")
    deps = ["kokoro-onnx", "onnxruntime", "soundfile"]
    try:
        subprocess.check_call([sys.executable, "-m", "pip", "install"] + deps)
        print("Successfully installed dependencies.")
    except Exception as e:
        print(f"Error installing dependencies: {e}")
        sys.exit(1)

def download_file(url, dest):
    if os.path.exists(dest):
        print(f"File already exists: {dest}")
        return
    
    print(f"Downloading {url} to {dest}...")
    try:
        # Create directory if it doesn't exist
        os.makedirs(os.path.dirname(dest), exist_ok=True)
        
        # Simple progress reporter
        def report(block_num, block_size, total_size):
            downloaded = block_num * block_size
            if total_size > 0:
                percent = downloaded * 100 / total_size
                print(f"Progress: {percent:.1f}%", end="\r")

        urllib.request.urlretrieve(url, dest, reporthook=report)
        print(f"\nSuccessfully downloaded {dest}")
    except Exception as e:
        print(f"\nError downloading {url}: {e}")

def main():
    # Target directory in Laravel storage
    # Assuming script is run from project root: python app/Services/Media/setup_kokoro.py
    base_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), "../../../storage/app/models/kokoro"))
    
    # Model and Voices URLs
    model_url = "https://github.com/thewh1teagle/kokoro-onnx/releases/download/model-files/kokoro-v0_19.onnx"
    voices_url = "https://github.com/thewh1teagle/kokoro-onnx/releases/download/model-files-v1.0/voices-v1.0.bin"
    
    install_deps()
    
    print("\n--- Downloading Model Files ---")
    download_file(model_url, os.path.join(base_dir, "kokoro-v0_19.onnx"))
    download_file(voices_url, os.path.join(base_dir, "voices.bin"))
    
    print("\n--- Setup Complete ---")
    print("You can now generate high-quality AI narration locally within the platform.")

if __name__ == "__main__":
    main()
