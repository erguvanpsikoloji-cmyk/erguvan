import os

css_path = r'c:\Users\ceren\.gemini\antigravity\scratch\uzmanpsikolog_sena_ceren\assets\css\style.css'

with open(css_path, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# 1. Optimize .service-icon animations
old_service_icon = """.service-card .service-icon {
    width: 80px;
    height: 80px;
    background: var(--bg-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    transition: all 0.3s ease;
    font-size: 2rem;
}"""

# Note: We need to find the exact match from the file. I'll use a more flexible replacement if needed.
# But I'll try exact first based on my previous Select-String output.

new_service_icon = """.service-card .service-icon {
    width: 80px;
    height: 80px;
    background: var(--bg-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), color 0.3s ease, box-shadow 0.3s ease;
    font-size: 2rem;
    position: relative;
    z-index: 1;
}

.service-card .service-icon::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--primary);
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}"""

old_service_hover = """.service-card:hover .service-icon {
    background: var(--primary);
    color: white;
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 10px 20px rgba(236, 72, 153, 0.3);
}"""

new_service_hover = """.service-card:hover .service-icon {
    color: white;
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 10px 20px rgba(236, 72, 153, 0.3);
}

.service-card:hover .service-icon::before {
    opacity: 1;
}"""

content = content.replace(old_service_icon, new_service_icon)
content = content.replace(old_service_hover, new_service_hover)

# 2. Optimize .testimonials-nav
old_test_nav = """.testimonials-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: white;
    border: none;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    z-index: 10;
    color: var(--primary);
}"""

new_test_nav = """.testimonials-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: white;
    border: none;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease, color 0.3s ease;
    z-index: 10;
    color: var(--primary);
}

.testimonials-nav::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--primary);
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}"""

old_test_hover = """.testimonials-nav:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 20px rgba(236, 72, 153, 0.5);
}"""

new_test_hover = """.testimonials-nav:hover {
    color: white;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 20px rgba(236, 72, 153, 0.5);
}

.testimonials-nav:hover::before {
    opacity: 1;
}"""

content = content.replace(old_test_nav, new_test_nav)
content = content.replace(old_test_hover, new_test_hover)

# 3. Add global GPU hints
if ".service-icon, .btn, .nav-toggle, .testimonials-nav" not in content:
    content += "\\n\\n/* Performance Optimizations */\\n.service-icon, .btn, .nav-toggle, .testimonials-nav { will-change: transform, opacity; }\\n"

with open(css_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("CSS Optimization complete.")
