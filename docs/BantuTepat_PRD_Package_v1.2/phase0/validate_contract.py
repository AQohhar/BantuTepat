from pathlib import Path
import json, re, hashlib
from jsonschema import Draft202012Validator
root=Path(__file__).resolve().parent.parent
api=json.loads((root/'phase0/openapi.phase0.json').read_text())
paths=api['paths']; schemas=api['components']['schemas']; actual={(m.upper(),path) for path,ops in paths.items() for m in ops}
assert len(actual)==63
text=(root/'04_API_Contract.md').read_text()
legacy=text.split('### 16.1 API publik',1)[1].split('### 16.4 Internal service',1)[0]
expected={(m,re.sub(r':(\w+)',r'{\1}',path)) for m,path in re.findall(r'\| (GET|POST|PATCH) \| `([^`]+)`',legacy)}
extra=text.split('### 16.7 Endpoint Phase 0',1)[1].split('### 20.4',1)[0]
expected |= set(re.findall(r'\| (GET|POST|PATCH) \| (/[^ ]+) \|',extra))
auth=(root/'phase0/AUTH_SECURITY.md').read_text()
expected |= set(re.findall(r'\| (GET|POST|PATCH) (/auth/[^ ]+) \|',auth))
assert actual==expected,(actual-expected,expected-actual)
ids=[]
for path,ops in paths.items():
 for method,op in ops.items():
  ids.append(op['operationId']); assert set(re.findall(r'{(\w+)}',path))=={x['name'] for x in op.get('parameters',[]) if x['in']=='path' and x.get('required')}
  assert any(k.startswith(('2','3')) for k in op['responses'])
  for auth in op.get('security',[]):assert set(auth)<=set(api['components']['securitySchemes'])
assert len(ids)==len(set(ids))
def walk(x):
 if isinstance(x,dict):
  if '$ref' in x:
   y=api
   for part in x['$ref'].split('/')[1:]:y=y[part]
  for v in x.values():walk(v)
 elif isinstance(x,list):
  for v in x:walk(v)
walk(api)
for schema in schemas.values():Draft202012Validator.check_schema(schema)
assert 'RESUB --> V' not in (root/'06_Workflow.md').read_text()
assert '| Versi | 1.2' in (root/'01_PRD.md').read_text()
assert 'appeal.assign | Reviewer assigned' in (root/'phase0/STATE_CONTRACT.md').read_text()
manifest=root/'phase0/MANIFEST.sha256'
if manifest.exists():
 for line in manifest.read_text().splitlines():
  digest,name=line.split('  ',1); assert hashlib.sha256((root/name).read_bytes()).hexdigest()==digest,name
print(f'PASS: {len(actual)} operations match inventory, {len(schemas)} valid JSON Schema declarations, references, unique operationIds, path parameters, metadata, resubmit and assignment policy, manifest (if present).')
print('Not full OpenAPI conformance or runtime/security/Prisma testing.')
