import re

filepath = 'app/Http/Requests/StoreEmployeeRequest.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace 'required' with 'nullable' in the rules array
# We need to be careful with 'required' inside arrays or strings.
# The easiest way is to use regex.

# Replace 'required|' with 'nullable|'
content = re.sub(r'\'required\|', "'nullable|", content)

# Replace 'required' in arrays: ['required', ...] -> ['nullable', ...]
content = re.sub(r'\'required\'', "'nullable'", content)

# We want to keep employee_id and agreed_to_terms as they were? The user says "remove all mandatory field".
# If they don't accept terms, it fails. I'll make agreed_to_terms nullable as well, or just leave 'accepted' (which requires it to be yes/on/1).
# If we remove 'required' from agreed_to_terms, we can change 'accepted' to 'nullable'.
content = re.sub(r'\'agreed_to_terms\' => \'accepted\'', "'agreed_to_terms' => 'nullable'", content)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
