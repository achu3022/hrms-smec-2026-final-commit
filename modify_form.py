import re

filepath = 'resources/views/employee/form.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace required attribute (need to be careful not to break other words containing 'required')
# Let's replace ' required ' with ' ' and 'required>' with '>'
content = re.sub(r'\srequired\b', '', content)
content = content.replace('<span class="text-danger">*</span>', '')

replacement = """<button type="button" class="btn btn-secondary btn-submit px-5 me-3" onclick="if(window.self !== window.top) { window.parent.postMessage('profile_skipped', '*'); } else { window.location.href='{{ route('employee.dashboard') }}'; }">Skip this form</button>
            <button type="submit" class="btn btn-success btn-submit px-5">"""

content = re.sub(r'<button type="submit" class="btn btn-success btn-submit px-5">', replacement, content)

# Additionally, the controller returns a JSON response on success which reloads the window. We should make it postMessage to parent if in iframe.
ajax_success_replacement = """if (response.redirect) {
                            if(window.self !== window.top) {
                                window.parent.postMessage('profile_completed', '*');
                            } else {
                                window.location.href = response.redirect;
                            }
                        } else {
                            if(window.self !== window.top) {
                                window.parent.postMessage('profile_completed', '*');
                            } else {
                                window.location.reload();
                            }
                        }"""
content = re.sub(r'if \(response\.redirect\) \{[\s\S]*?window\.location\.reload\(\);\n                        \}', ajax_success_replacement, content, count=1)


with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
