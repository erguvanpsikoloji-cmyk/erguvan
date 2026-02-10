import re

input_file = 'style.css'
output_file = 'style.min.css'

with open(input_file, 'r', encoding='utf-8') as f:
    css = f.read()

# Remove comments
css = re.sub(r'/\*.*?\*/', '', css, flags=re.DOTALL)
# Remove newlines and tabs
css = css.replace('\n', '').replace('\t', '')
# Collapse multiple spaces
css = re.sub(r'\s+', ' ', css)
# Remove spaces around punctuation
css = re.sub(r'\s*([:;{}])\s*', r'\1', css)
# Remove last semicolon in block
css = html = re.sub(r';}', '}', css)

with open(output_file, 'w', encoding='utf-8') as f:
    f.write(css)

print(f"Minified {input_file} to {output_file}")
