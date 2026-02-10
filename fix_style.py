
import os

file_path = 'assets/css/style.css'

with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
skip = False
for line in lines:
    if "Custom Scrollbar" in line:
        skip = True
    
    if not skip:
        new_lines.append(line)
        
    # If we were skipping, checking if this line ends the block? 
    # Actually, the block is short. I'll just skip lines containing '::-webkit-scrollbar'
    if skip and "background-color: #db2777" in line:
        skip = False

# Cleaner approach: Filter out lines related to custom scrollbar
cleaned_lines = [line for line in lines if "webkit-scrollbar" not in line and "Custom Scrollbar" not in line]

# Add the Fix
cleaned_lines.append("\n/* --- FIX DOUBLE SCROLLBAR (Tek Çubuk) --- */\n")
cleaned_lines.append("html {\n")
cleaned_lines.append("    overflow-y: scroll !important;\n")
cleaned_lines.append("    height: 100% !important;\n")
cleaned_lines.append("}\n")
cleaned_lines.append("body {\n")
cleaned_lines.append("    overflow-y: visible !important;\n")
cleaned_lines.append("    height: auto !important;\n")
cleaned_lines.append("    min-height: 100%;\n")
cleaned_lines.append("}\n")

with open(file_path, 'w', encoding='utf-8') as f:
    f.writelines(cleaned_lines)
