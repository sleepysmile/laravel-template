## Use cases

### Model
```php
use Illuminate\Support\Facades\Storage;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\ModelFilesystem;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;

$storage = Storage::disk();
$adapter = new FilesystemAdapter($storage);

$fs = new ModelFilesystem($adapter);

$savedPath = $fs->store(
                $someModel,
                'someAttribute',
                FileAdapter::fromUploadedFile($uploadFile)
            );
```

### Static
```php
use Illuminate\Support\Facades\Storage;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\StaticFilesystem;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;

$storage = Storage::disk();
$adapter = new FilesystemAdapter($storage);

$fs = new StaticFilesystem($adapter);

$savedPath = $fs->store(FileAdapter::fromUploadedFile($uploadFile));
```

### Resource
```php
use Illuminate\Support\Facades\Storage;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\ResourceFilesystem;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;

$storage = Storage::disk();
$adapter = new FilesystemAdapter($storage);

$fs = new ResourceFilesystem($adapter);


```
