import urllib.request
import re

url = "https://storybeeai.com/assets/index-Cfo0Zxu3.js"
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
try:
    with urllib.request.urlopen(req) as response:
        content = response.read().decode('utf-8')
    
    # Extract long strings that look like text content
    strings = re.findall(r'"([^"\\]*[\s\w,.-]{20,})"', content)
    strings.extend(re.findall(r'`([^`\\]*[\s\w,.-]{20,})`', content))
    
    with open("storybee_content.txt", "w", encoding="utf-8") as f:
        for s in set(strings):
            if len(s) > 20 and "function" not in s and "<" not in s and "{" not in s:
                f.write(s.replace('\n', ' ').strip() + "\n")
    print("Success")
except Exception as e:
    print(e)
