# API Reference

Complete method reference for all API sections available through `QPlayClient`.

> **Official API Documentation**: [Q-Play REST API Docs](https://developer.q-play.io/docs/api-docs/qplay-rest)
> **Production Base URL**: `https://api.q-play.io/api/rest/v2`

## Accessing Sections

Each section is accessed via a typed accessor on the `QPlayClient` instance:

```php
$api = new QPlayClient(/* ... */);

$media  = $api->media();
$players = $api->players();
$presentations = $api->presentations();
$playlist = $api->playlist();
$integration = $api->integration();
$integrationTemplate = $api->integrationTemplate();
$app = $api->app();
$appTemplate = $api->appTemplate();
$system = $api->system();
```

All methods return `string|array` — JSON responses are decoded to arrays; non-JSON responses (e.g., binary downloads) return strings.

---

## Generic Section Methods

Every section extends `AbstractSection` and provides these generic HTTP methods for endpoints not covered by convenience methods:

| Method | Description |
|---|---|
| `get(string $path = '', array $query = [], array $headers = [])` | GET request |
| `post(string $path = '', array $body = [], array $headers = [])` | POST request |
| `put(string $path = '', array $body = [], array $headers = [])` | PUT request |
| `patch(string $path = '', array $body = [], array $headers = [])` | PATCH request |
| `delete(string $path = '', array $query = [], array $headers = [])` | DELETE request |
| `request(string $method, string $path = '', array $options = [])` | Generic request with any HTTP method |

```php
// Example: call an endpoint not covered by a convenience method
$response = $api->media()->get('custom-endpoint', ['filter' => 'value']);
$response = $api->media()->post('custom-endpoint', ['key' => 'value']);
```

---

## System Section

Access: `$api->system()`

| Method | Endpoint | Description |
|---|---|---|
| `status()` | `GET /status` | Get system status |
| `health()` | `GET /health` | Get system health |

```php
$status = $api->system()->status();
$health = $api->system()->health();
```

---

## Media Library Section

Access: `$api->media()`

### Folders

| Method | Endpoint | Description |
|---|---|---|
| `listFolders(array $query = [])` | `GET /media/folder` | List media folders |
| `getFolder(int\|string $folderId)` | `GET /media/folder/{id}` | Get a folder by ID |
| `createFolder(array $data)` | `POST /media/folder` | Create a folder |
| `updateFolder(int\|string $folderId, array $data)` | `PATCH /media/folder/{id}` | Update a folder |
| `deleteFolder(int\|string $folderId)` | `DELETE /media/folder/{id}` | Delete a folder |

### Fonts

| Method | Endpoint | Description |
|---|---|---|
| `listFonts(array $query = [])` | `GET /media/font` | List available fonts |
| `downloadFont(string $uuid, int\|string $id, string $filename)` | `GET /media/font/{uuid}/{id}/download/{filename}` | Download a font file |

### Files

| Method | Endpoint | Description |
|---|---|---|
| `listFiles(array $query = [])` | `GET /media/file` | List media files |
| `getFile(int\|string $fileId)` | `GET /media/file/{id}` | Get a file by ID |
| `createFile(array $data)` | `POST /media/file` | Create/upload a file |
| `deleteFile(int\|string $fileId)` | `DELETE /media/file/{id}` | Delete a file |
| `downloadFile(int\|string $fileId)` | `GET /media/file/{id}/download` | Download a file |
| `regenerateFile(int\|string $fileId, array $data = [])` | `PUT /media/file/{id}/regenerate` | Regenerate a file |

### Backward Compatibility Aliases

| Alias | Maps to |
|---|---|
| `list()` | `listFiles()` |
| `getById()` | `getFile()` |
| `create()` | `createFile()` |
| `deleteById()` | `deleteFile()` |
| `download()` | `downloadFile()` |

```php
// Folders
$folders = $api->media()->listFolders(['page' => 1]);
$folder = $api->media()->getFolder(1);
$newFolder = $api->media()->createFolder(['name' => 'Marketing Assets']);
$api->media()->updateFolder(1, ['name' => 'Brand Assets']);
$api->media()->deleteFolder(1);

// Files
$files = $api->media()->listFiles(['folder_id' => 1]);
$file = $api->media()->getFile('file-uuid');
$api->media()->downloadFile('file-uuid');
$api->media()->regenerateFile('file-uuid');
$api->media()->deleteFile('file-uuid');
```

---

## Players Section

Access: `$api->players()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /players` | List all players |
| `getById(int\|string $playerId)` | `GET /players/{id}` | Get a player by ID |
| `create(array $data)` | `POST /players` | Create a player |
| `update(int\|string $playerId, array $data)` | `PATCH /players/{id}` | Update a player |
| `deleteById(int\|string $playerId)` | `DELETE /players/{id}` | Delete a player |
| `getStatus(int\|string $playerId)` | `GET /players/{id}/status` | Get player runtime status |
| `getScreenshot(int\|string $playerId)` | `GET /players/{id}/screenshot` | Get player screenshot |
| `getBlackscreenSchedule(int\|string $playerId)` | `GET /players/{id}/blackscreen-schedule` | Get blackscreen schedule |
| `sendCommand(int\|string $playerId, array $data = [])` | `POST /players/{id}/commands` | Send command to player |
| `unlinkPresentation(int\|string $playerId, int\|string $presentationVersionId)` | `DELETE /players/{id}/presentations/{versionId}` | Unlink presentation |
| `unlinkEvent(int\|string $playerId, int\|string $eventId)` | `DELETE /players/{id}/events/{eventId}` | Unlink event |

```php
$players = $api->players()->list();
$player = $api->players()->getById(10);
$api->players()->update(10, ['name' => 'Lobby Screen']);
$status = $api->players()->getStatus(10);
$api->players()->sendCommand(10, ['action' => 'reload']);
```

---

## Presentations Section

Access: `$api->presentations()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /presentation` | List presentations |
| `getById(int\|string $id)` | `GET /presentation/{id}` | Get a presentation |
| `create(array $data)` | `POST /presentation` | Create a presentation |
| `update(int\|string $id, array $data)` | `PATCH /presentation/{id}` | Update a presentation |
| `deleteById(int\|string $id)` | `DELETE /presentation/{id}` | Delete a presentation |

```php
$presentations = $api->presentations()->list();
$presentation = $api->presentations()->getById(5);
$api->presentations()->update(5, ['title' => 'Updated Title']);
```

---

## Playlist Section

Access: `$api->playlist()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /playlist` | List playlists |
| `getById(int\|string $id)` | `GET /playlist/{id}` | Get a playlist |
| `create(array $data)` | `POST /playlist` | Create a playlist |
| `update(int\|string $id, array $data)` | `PATCH /playlist/{id}` | Update a playlist |
| `deleteById(int\|string $id)` | `DELETE /playlist/{id}` | Delete a playlist |

```php
$playlists = $api->playlist()->list();
$playlist = $api->playlist()->getById(2);
$api->playlist()->update(2, ['name' => 'Morning Playlist']);
```

---

## Integration Section

Access: `$api->integration()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /integration` | List integrations |
| `getById(int\|string $id)` | `GET /integration/{id}` | Get an integration |
| `run(int\|string $id, array $payload = [])` | `POST /integration/{id}/run` | Trigger a run *(deprecated)* |
| `statistics(int\|string $id, array $query = [])` | `GET /integration/{id}/statistics` | Fetch statistics *(deprecated)* |

> **Note:** The `run()` and `statistics()` methods are marked as deprecated — the endpoints may not be defined in all API versions.

```php
$integrations = $api->integration()->list();
$integration = $api->integration()->getById(1);
```

---

## Integration Template Section

Access: `$api->integrationTemplate()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /integration-template` | List templates |
| `getById(int\|string $id)` | `GET /integration-template/{id}` | Get a template |
| `listVersions(int\|string $templateId, array $query = [])` | `GET /integration-template/{id}/version` | List versions |
| `getVersion(int\|string $templateId, int\|string $versionId)` | `GET /integration-template/{id}/version/{versionId}` | Get a version |
| `listScripts(int\|string $templateId, int\|string $versionId, array $query = [])` | `GET /integration-template/{id}/version/{v}/script` | List scripts |
| `getScript(int\|string $templateId, int\|string $versionId, int\|string $identifier)` | `GET /integration-template/{id}/version/{v}/script/{identifier}` | Get a script |

```php
$templates = $api->integrationTemplate()->list();
$versions = $api->integrationTemplate()->listVersions(1);
$scripts = $api->integrationTemplate()->listScripts(1, 2);
```

---

## App Section (Read-Only)

Access: `$api->app()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /app` | List apps |
| `getById(string $id)` | `GET /app/{id}` | Get an app |
| `listVersions(string $appId, array $query = [])` | `GET /app/{id}/version` | List versions |
| `getVersion(string $appId, string $version, int\|string $patchVersion)` | `GET /app/{id}/version/{version}.{patch}` | Get a version |

```php
$apps = $api->app()->list();
$app = $api->app()->getById('my-app');
$versions = $api->app()->listVersions('my-app');
```

---

## App Template Section

Access: `$api->appTemplate()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /app-template` | List app templates |
| `getById(int\|string $id)` | `GET /app-template/{id}` | Get a template |
| `listVersions(int\|string $templateId, array $query = [])` | `GET /app-template/{id}/version` | List versions |
| `getVersion(int\|string $templateId, int\|string $version, int\|string\|null $patchVersion = null)` | `GET /app-template/{id}/version/{version}` | Get a version |

```php
$templates = $api->appTemplate()->list();
$template = $api->appTemplate()->getById(1);
$version = $api->appTemplate()->getVersion(1, '1', '0');
```

---

## Developer Sections

Developer sections provide full CRUD operations for managing apps, templates, and their media assets.

### Developer Apps

Access: `$api->developerApp()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /developer/app` | List apps |
| `getById(int\|string $id)` | `GET /developer/app/{id}` | Get an app |
| `create(array $data)` | `POST /developer/app` | Create an app |
| `update(int\|string $id, array $data)` | `PATCH /developer/app/{id}` | Update an app |
| `deleteById(int\|string $id)` | `DELETE /developer/app/{id}` | Delete an app |
| `listVersions(int\|string $appId, array $query = [])` | `GET /developer/app/{id}/version` | List versions |
| `getVersion(int\|string $appId, int\|string $version, int\|string\|null $patchVersion = null)` | `GET /developer/app/{id}/version/{v}` | Get a version |
| `createVersion(int\|string $appId, array $data)` | `POST /developer/app/{id}/version` | Create a version |
| `updateVersion(int\|string $appId, int\|string $version, int\|string\|array $patchVersion, array $data = [])` | `PATCH /developer/app/{id}/version/{v}` | Update a version |
| `deleteVersion(int\|string $appId, int\|string $version, int\|string\|null $patchVersion = null)` | `DELETE /developer/app/{id}/version/{v}` | Delete a version |

```php
$apps = $api->developerApp()->list();
$app = $api->developerApp()->create(['name' => 'My App']);
$api->developerApp()->update(1, ['title' => 'Updated Title']);
$api->developerApp()->createVersion(1, ['config' => '...']);
$api->developerApp()->updateVersion(1, '1', '0', ['config' => 'updated']);
```

### Developer App Templates

Access: `$api->developerAppTemplate()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /developer/app-template` | List templates |
| `getById(int\|string $id)` | `GET /developer/app-template/{id}` | Get a template |
| `create(array $data)` | `POST /developer/app-template` | Create a template |
| `update(int\|string $id, array $data)` | `PATCH /developer/app-template/{id}` | Update a template |
| `deleteById(int\|string $id)` | `DELETE /developer/app-template/{id}` | Delete a template |
| `listVersions(int\|string $templateId, array $query = [])` | `GET /developer/app-template/{id}/version` | List versions |
| `getVersion(int\|string $templateId, int\|string $version, int\|string\|null $patchVersion = null)` | `GET /developer/app-template/{id}/version/{v}` | Get a version |
| `createVersion(int\|string $templateId, array $data)` | `POST /developer/app-template/{id}/version` | Create a version |
| `updateVersion(int\|string $templateId, int\|string $version, int\|string\|array $patchVersion, array $data = [])` | `PATCH /developer/app-template/{id}/version/{v}` | Update a version |
| `deleteVersion(int\|string $templateId, int\|string $version, int\|string\|null $patchVersion = null)` | `DELETE /developer/app-template/{id}/version/{v}` | Delete a version |

### Developer Integration Templates

Access: `$api->developerIntegrationTemplate()`

| Method | Endpoint | Description |
|---|---|---|
| `list(array $query = [])` | `GET /developer/integration-template` | List templates |
| `getById(int\|string $id)` | `GET /developer/integration-template/{id}` | Get a template |
| `create(array $data)` | `POST /developer/integration-template` | Create a template |
| `update(int\|string $id, array $data)` | `PATCH /developer/integration-template/{id}` | Update a template |
| `deleteById(int\|string $id)` | `DELETE /developer/integration-template/{id}` | Delete a template |
| `listVersions(int\|string $templateId, array $query = [])` | `GET /developer/integration-template/{id}/version` | List versions |
| `getVersion(int\|string $templateId, int\|string $versionId)` | `GET /developer/integration-template/{id}/version/{v}` | Get a version |
| `createVersion(int\|string $templateId, array $data)` | `POST /developer/integration-template/{id}/version` | Create a version |
| `updateVersion(int\|string $templateId, int\|string $versionId, array $data)` | `PATCH /developer/integration-template/{id}/version/{v}` | Update a version |
| `deleteVersion(int\|string $templateId, int\|string $versionId)` | `DELETE /developer/integration-template/{id}/version/{v}` | Delete a version |
| `listScripts(int\|string $templateId, int\|string $versionId, array $query = [])` | `GET /developer/integration-template/{id}/version/{v}/script` | List scripts |
| `createScript(int\|string $templateId, int\|string $versionId, array $data)` | `POST /developer/integration-template/{id}/version/{v}/script` | Create a script |
| `getScript(int\|string $templateId, int\|string $versionId, int\|string $identifier)` | `GET /developer/integration-template/{id}/version/{v}/script/{id}` | Get a script |
| `updateScript(int\|string $templateId, int\|string $versionId, int\|string $identifier, array $data)` | `PATCH /developer/integration-template/{id}/version/{v}/script/{id}` | Update a script |
| `deleteScript(int\|string $templateId, int\|string $versionId, int\|string $identifier)` | `DELETE /developer/integration-template/{id}/version/{v}/script/{id}` | Delete a script |

### Developer App Media

Access: `$api->developerAppMedia()`

| Method | Endpoint | Description |
|---|---|---|
| `getMedia(int\|string $appId, string $version)` | `GET /developer/app/{id}/version/{v}/media` | Get media assets |
| `uploadMedia(int\|string $appId, string $version, array $data)` | `POST /developer/app/{id}/version/{v}/media` | Upload media |
| `deleteMedia(int\|string $appId, string $version, string $fileUuid)` | `DELETE /developer/app/{id}/version/{v}/media/{uuid}` | Delete media |
| `uploadIcon(int\|string $appId, string $version, array $data)` | `POST /developer/app/{id}/version/{v}/media/icon` | Upload app icon |
| `removeIcon(int\|string $appId, string $version)` | `DELETE /developer/app/{id}/version/{v}/media/icon` | Remove app icon |

```php
$media = $api->developerAppMedia()->getMedia(1, '1.0.0');
$api->developerAppMedia()->uploadIcon(1, '1.0.0', ['icon' => 'base64-data']);
$api->developerAppMedia()->removeIcon(1, '1.0.0');
```

### Developer App Template Media

Access: `$api->developerAppTemplateMedia()`

| Method | Endpoint | Description |
|---|---|---|
| `getMedia(int\|string $templateId, string $version)` | `GET /developer/app-template/{id}/version/{v}/media` | Get media assets |
| `uploadMedia(int\|string $templateId, string $version, array $data)` | `POST /developer/app-template/{id}/version/{v}/media` | Upload media |
| `deleteMedia(int\|string $templateId, string $version, string $fileUuid)` | `DELETE /developer/app-template/{id}/version/{v}/media/{uuid}` | Delete media |
