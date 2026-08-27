#!/bin/bash

# Accept one argument: the module name (also used for the model)
MODULE_NAME=$1
MODEL_NAME=$1

if [ -z "$MODULE_NAME" ]; then
    echo "❌ Usage: ./make-module.sh ModuleName"
    exit 1
fi

# Fix path to the correct model location
MODEL_PATH="app/${MODEL_NAME}.php"

# Check if model file exists, if not - create it
if [ ! -f "$MODEL_PATH" ]; then
    echo "⚠️ Model '$MODEL_NAME' does not exist in app/Models."
    echo "📦 Creating model automatically..."
    php artisan make:model $MODEL_NAME
    echo "✅ Model '$MODEL_NAME' created."
else
    echo "✅ Model '$MODEL_NAME' already exists."
fi

# Check if module already exists (any of the files)
FILE_EXISTS=false
for FILE in \
    "app/Interfaces/${MODULE_NAME}Interface.php" \
    "app/Repositories/${MODULE_NAME}Repository.php" \
    "app/Services/${MODULE_NAME}Service.php" \
    "app/Http/Controllers/${MODULE_NAME}Controller.php" \
    "app/Http/Requests/${MODULE_NAME}Request.php"
do
    if [ -f "$FILE" ]; then
    FILE_EXISTS=true
    break
    fi
done

if [ "$FILE_EXISTS" = true ]; then
    echo "⚠️ Module '$MODULE_NAME' already exists. Overwrite? [y/N]"
    read confirm
    if [[ $confirm != "y" && $confirm != "Y" ]]; then
        echo "❌ Operation aborted."
        exit 1
    fi
fi

echo "🔧 Generating Interface, Repository, Service, Controller, and Request for: $MODULE_NAME using model: $MODEL_NAME"

# Create directories
mkdir -p app/Interfaces
mkdir -p app/Repositories
mkdir -p app/Services
mkdir -p app/Http/Controllers
mkdir -p app/Http/Requests

# Interface
cat <<EOL > app/Interfaces/${MODULE_NAME}Interface.php
<?php

namespace App\Interfaces;

interface ${MODULE_NAME}Interface
{
    public function all();
    public function find(\$id);
    public function create(array \$data);
}
EOL

# Repository
cat <<EOL > app/Repositories/${MODULE_NAME}Repository.php
<?php

namespace App\Repositories;

use App\Models\\$MODEL_NAME;
use App\Interfaces\\${MODULE_NAME}Interface;

class ${MODULE_NAME}Repository implements ${MODULE_NAME}Interface
{
    public function all()
    {
        return $MODEL_NAME::all();
    }

    public function find(\$id)
    {
        return $MODEL_NAME::findOrFail(\$id);
    }

    public function create(array \$data)
    {
        return $MODEL_NAME::create(\$data);
    }
}
EOL

# Service
cat <<EOL > app/Services/${MODULE_NAME}Service.php
<?php

namespace App\Services;

use App\Interfaces\\${MODULE_NAME}Interface;

class ${MODULE_NAME}Service
{
    protected \$repo;

    public function __construct(${MODULE_NAME}Interface \$repo)
    {
        \$this->repo = \$repo;
    }

    public function getAll()
    {
        return \$this->repo->all();
    }

    public function getById(\$id)
    {
        return \$this->repo->find(\$id);
    }

    public function create(array \$data)
    {
        return \$this->repo->create(\$data);
    }
}
EOL

# Request
cat <<EOL > app/Http/Requests/${MODULE_NAME}Request.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ${MODULE_NAME}Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // TODO: Customize these validation rules
            'name' => 'required|string',
        ];
    }
}
EOL

# Controller
cat <<EOL > app/Http/Controllers/${MODULE_NAME}Controller.php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\\${MODULE_NAME}Request;
use App\Services\\${MODULE_NAME}Service;
use Illuminate\Http\Request;

class ${MODULE_NAME}Controller extends Controller
{
    protected \$service;

    public function __construct(${MODULE_NAME}Service \$service)
    {
        \$this->service = \$service;
    }

    public function index()
    {
        return response()->json(\$this->service->getAll());
    }

    public function show(\$id)
    {
        return response()->json(\$this->service->getById(\$id));
    }

    public function store(${MODULE_NAME}Request \$request)
    {
        \$data = \$request->validated();
        return response()->json(\$this->service->create(\$data));
    }
}
EOL

echo "✅ Module '$MODULE_NAME' generated successfully using model '$MODEL_NAME'!"
