<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateCrud extends Command
{
    protected $signature = 'make:crud {name : Nombre de la entidad a generar}';
    protected $description = 'Genera CRUD completo (migración, modelo, controlador y páginas Vue) para una entidad';

    private array $defaultFields = [
        'nombre' => 'string',
        'descripcion' => 'text',
        'precio' => 'decimal:8,2',
        'activo' => 'boolean',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    public function handle(): int
    {
        $name = $this->argument('name');
        
        if (empty($name)) {
            $this->error('Debe proporcionar un nombre para la entidad');
            return 0;
        }

        $this->info("Generando CRUD para: {$name}");
        
        $this->createMigration($name);
        $this->createModel($name);
        $this->createController($name);
        $this->createVuePages($name);
        $this->addRoutes($name);
        
        $this->info("✅ CRUD generado exitosamente para: {$name}");
        return 1;
    }

    private function createMigration(string $name): void
    {
        $tableName = Str::plural(Str::snake($name));
        $this->call('make:migration', [
            'name' => "create_{$tableName}_table",
            '--create' => $tableName
        ]);

        $migrationFile = collect(glob(database_path('migrations/*.php')))
            ->last(fn($file) => str_contains($file, "create_{$tableName}_table"));

        if ($migrationFile && File::exists($migrationFile)) {
            $content = File::get($migrationFile);
            
            $columns = '';
            foreach ($this->defaultFields as $fieldName => $fieldType) {
                $columns .= $this->generateColumnDefinition($fieldName, $fieldType) . "\n            ";
            }

            $content = preg_replace(
                '/\$table->id\(\);/',
                "$0\n            $columns",
                $content
            );

            File::put($migrationFile, $content);
            $this->info("✓ Migración creada");
        }
    }

    private function createModel(string $name): void
    {
        $this->call('make:model', ['name' => $name]);
        
        $modelPath = app_path("Models/{$name}.php");
        
        if (File::exists($modelPath)) {
            $content = File::get($modelPath);
            
            $fillable = array_keys($this->defaultFields);
            $fillableString = implode("', '", $fillable);
            
            $content = preg_replace(
                '/protected \$fillable = \[.*?\];/s',
                "protected \$fillable = ['{$fillableString}'];",
                $content
            );

            if (!str_contains($content, 'use Illuminate\Database\Eloquent\SoftDeletes;')) {
                $content = str_replace(
                    'use Illuminate\Database\Eloquent\Model;',
                    "use Illuminate\Database\Eloquent\Model;\nuse Illuminate\Database\Eloquent\SoftDeletes;",
                    $content
                );
            }

            if (!str_contains($content, 'use SoftDeletes;')) {
                $content = preg_replace(
                    '/class ' . $name . ' extends/',
                    "use SoftDeletes;\n\n    class " . $name . " extends",
                    $content
                );
            }

            File::put($modelPath, $content);
            $this->info("✓ Modelo creado");
        }
    }

    private function createController(string $name): void
    {
        $this->call('make:controller', [
            'name' => "{$name}Controller",
            '--resource' => true,
            '--model' => $name
        ]);
        
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");
        
        if (File::exists($controllerPath)) {
            $content = File::get($controllerPath);
            
            $content = str_replace(
                'use App\\Models\\' . $name . ';',
                "use App\\Models\\{$name};\nuse Illuminate\\Http\\Request;\nuse Inertia\\Inertia;",
                $content
            );

            $indexMethod = $this->generateIndexMethod($name);
            $createMethod = $this->generateCreateMethod($name);
            $storeMethod = $this->generateStoreMethod($name);
            $editMethod = $this->generateEditMethod($name);
            $updateMethod = $this->generateUpdateMethod($name);
            $destroyMethod = $this->generateDestroyMethod($name);

            $content = preg_replace('/public function index\(\).*?public function create\(/s', $indexMethod . "\n\n    public function create(", $content);
            $content = preg_replace('/public function create\(\).*?public function store\(/s', $createMethod . "\n\n    public function store(", $content);
            $content = preg_replace('/public function store\(\).*?public function edit\(/s', $storeMethod . "\n\n    public function edit(", $content);
            $content = preg_replace('/public function edit\(\).*?public function update\(/s', $editMethod . "\n\n    public function update(", $content);
            $content = preg_replace('/public function update\(\).*?public function destroy\(/s', $updateMethod . "\n\n    public function destroy(", $content);
            $content = preg_replace('/public function destroy\(.*?\).*?}/s', $destroyMethod, $content);

            File::put($controllerPath, $content);
            $this->info("✓ Controlador creado");
        }
    }

    private function createVuePages(string $name): void
    {
        $vuePath = resource_path("js/Pages/" . ucfirst($name));
        
        if (!File::exists($vuePath)) {
            File::makeDirectory($vuePath, 0755, true);
        }

        $this->createIndexVue($vuePath, $name);
        $this->createCreateVue($vuePath, $name);
        $this->createEditVue($vuePath, $name);
        $this->createDeletebulkVue($vuePath, $name);
        
        $this->info("✓ Páginas Vue creadas");
    }

    private function createIndexVue(string $path, string $name): void
    {
        $content = $this->getIndexVueContent($name);
        File::put("{$path}/Index.vue", $content);
    }

    private function createCreateVue(string $path, string $name): void
    {
        $content = $this->getCreateVueContent($name);
        File::put("{$path}/Create.vue", $content);
    }

    private function createEditVue(string $path, string $name): void
    {
        $content = $this->getEditVueContent($name);
        File::put("{$path}/Edit.vue", $content);
    }

    private function createDeletebulkVue(string $path, string $name): void
    {
        $content = $this->getDeletebulkVueContent($name);
        File::put("{$path}/Deletebulk.vue", $content);
    }

    private function addRoutes(string $name): void
    {
        $routeFile = base_path('routes/web.php');
        $content = File::get($routeFile);
        
        $newRoute = "Route::resource('".Str::kebab($name)."', App\Http\Controllers\\{$name}Controller::class);\n";
        
        if (!str_contains($content, $newRoute)) {
            $content .= "\n" . $newRoute;
            File::put($routeFile, $content);
            $this->info("✓ Rutas agregadas");
        }
    }

    private function generateColumnDefinition(string $name, string $type): string
    {
        return match($type) {
            'string' => "\$table->string('{$name}');",
            'text' => "\$table->text('{$name}')->nullable();",
            'decimal:8,2' => "\$table->decimal('{$name}', 8, 2)->default(0);",
            'boolean' => "\$table->boolean('{$name}')->default(true);",
            'timestamp' => "\$table->timestamp('{$name}')->nullable();",
            default => "\$table->{$type}('{$name}');"
        };
    }

    private function generateIndexMethod(string $name): string
    {
        $pluralName = Str::plural($name);
        return "public function index()
    {
        \${$pluralName} = {$name}::latest()->paginate(10);
        
        return Inertia::render('".ucfirst($name)."/Index', [
            '{$pluralName}' => \${$pluralName}
        ]);
    }";
    }

    private function generateCreateMethod(string $name): string
    {
        return "public function create()
    {
        return Inertia::render('".ucfirst($name)."/Create');
    }";
    }

    private function generateStoreMethod(string $name): string
    {
        return "public function store(Request \$request)
    {
        \$validated = \$request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'activo' => 'boolean'
        ]);

        {$name}::create(\$validated);

        return redirect()->route('".Str::kebab($name).".index')
            ->with('success', '{$name} creado exitosamente');
    }";
    }

    private function generateEditMethod(string $name): string
    {
        $lowerName = strtolower($name);
        return "public function edit({$name} \${$lowerName})
    {
        return Inertia::render('".ucfirst($name)."/Edit', [
            '{$lowerName}' => \${$lowerName}
        ]);
    }";
    }

    private function generateUpdateMethod(string $name): string
    {
        $lowerName = strtolower($name);
        return "public function update(Request \$request, {$name} \${$lowerName})
    {
        \$validated = \$request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'activo' => 'boolean'
        ]);

        \${$lowerName}->update(\$validated);

        return redirect()->route('".Str::kebab($name).".index')
            ->with('success', '{$name} actualizado exitosamente');
    }";
    }

    private function generateDestroyMethod(string $name): string
    {
        $lowerName = strtolower($name);
        return "public function destroy({$name} \${$lowerName})
    {
        \${$lowerName}->delete();

        return redirect()->route('".Str::kebab($name).".index')
            ->with('success', '{$name} eliminado exitosamente');
    }";
    }

    private function getIndexVueContent(string $name): string
    {
        $pluralName = Str::plural($name);
        $lowerPlural = strtolower($pluralName);
        $routeName = Str::kebab($name);
        
        return "<template>
  <AppLayout>
    <div class='py-12'>
      <div class='max-w-7xl mx-auto sm:px-6 lg:px-8'>
        <div class='bg-white overflow-hidden shadow-xl sm:rounded-lg'>
          <div class='p-6'>
            <div class='flex justify-between items-center mb-6'>
              <h2 class='text-2xl font-semibold text-gray-800'>Lista de {$pluralName}</h2>
              <Link :href='route(\"{$routeName}.create\")' class='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600'>
                Nuevo {$name}
              </Link>
            </div>
            
            <div class='overflow-x-auto'>
              <table class='min-w-full divide-y divide-gray-200'>
                <thead class='bg-gray-50'>
                  <tr>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Nombre</th>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Descripción</th>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Precio</th>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Activo</th>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Acciones</th>
                  </tr>
                </thead>
                <tbody class='bg-white divide-y divide-gray-200'>
                  <tr v-for='item in {$lowerPlural}' :key='item.id'>
                    <td class='px-6 py-4 whitespace-nowrap'>{{ item.nombre }}</td>
                    <td class='px-6 py-4'>{{ item.descripcion }}</td>
                    <td class='px-6 py-4 whitespace-nowrap'>{{ item.precio }}</td>
                    <td class='px-6 py-4 whitespace-nowrap'>
                      <span :class='item.activo ? \"bg-green-100 text-green-800\" : \"bg-red-100 text-red-800\"' class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full'>
                        {{ item.activo ? 'Activo' : 'Inactivo' }}
                      </span>
                    </td>
                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                      <Link :href='route(\"{$routeName}.edit\", item.id)' class='text-indigo-600 hover:text-indigo-900 mr-3'>Editar</Link>
                      <button @click='deleteItem(item.id)' class='text-red-600 hover:text-red-900'>Eliminar</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  {$lowerPlural}: Object
})

function deleteItem(id) {
  if (confirm('¿Está seguro de eliminar este {$name}?')) {
    router.delete(route('{$routeName}.destroy', id))
  }
}
</script>";
    }

    private function getCreateVueContent(string $name): string
    {
        $routeName = Str::kebab($name);
        
        return "<template>
  <AppLayout>
    <div class='py-12'>
      <div class='max-w-7xl mx-auto sm:px-6 lg:px-8'>
        <div class='bg-white overflow-hidden shadow-xl sm:rounded-lg'>
          <div class='p-6'>
            <h2 class='text-2xl font-semibold text-gray-800 mb-6'>Nuevo {$name}</h2>
            
            <form @submit.prevent='submit' class='space-y-6'>
              <div>
                <label class='block text-sm font-medium text-gray-700'>Nombre</label>
                <input v-model='form.nombre' type='text' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500' required>
                <div v-if='errors.nombre' class='text-red-500 text-sm mt-1'>{{ errors.nombre }}</div>
              </div>
              
              <div>
                <label class='block text-sm font-medium text-gray-700'>Descripción</label>
                <textarea v-model='form.descripcion' rows='3' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'></textarea>
                <div v-if='errors.descripcion' class='text-red-500 text-sm mt-1'>{{ errors.descripcion }}</div>
              </div>
              
              <div>
                <label class='block text-sm font-medium text-gray-700'>Precio</label>
                <input v-model='form.precio' type='number' step='0.01' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500' required>
                <div v-if='errors.precio' class='text-red-500 text-sm mt-1'>{{ errors.precio }}</div>
              </div>
              
              <div>
                <label class='flex items-center'>
                  <input v-model='form.activo' type='checkbox' class='rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'>
                  <span class='ml-2 text-sm text-gray-700'>Activo</span>
                </label>
                <div v-if='errors.activo' class='text-red-500 text-sm mt-1'>{{ errors.activo }}</div>
              </div>
              
              <div class='flex items-center gap-4'>
                <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600' :disabled='processing'>
                  Guardar
                </button>
                <Link :href='route(\"{$routeName}.index\")' class='text-gray-600 hover:text-gray-800'>
                  Cancelar
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  nombre: '',
  descripcion: '',
  precio: 0,
  activo: true
})

const props = defineProps({
  errors: Object
})

function submit() {
  form.post(route('{$routeName}.store'))
}
</script>";
    }

    private function getEditVueContent(string $name): string
    {
        $lowerName = strtolower($name);
        $routeName = Str::kebab($name);
        
        return "<template>
  <AppLayout>
    <div class='py-12'>
      <div class='max-w-7xl mx-auto sm:px-6 lg:px-8'>
        <div class='bg-white overflow-hidden shadow-xl sm:rounded-lg'>
          <div class='p-6'>
            <h2 class='text-2xl font-semibold text-gray-800 mb-6'>Editar {$name}</h2>
            
            <form @submit.prevent='submit' class='space-y-6'>
              <div>
                <label class='block text-sm font-medium text-gray-700'>Nombre</label>
                <input v-model='form.nombre' type='text' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500' required>
                <div v-if='errors.nombre' class='text-red-500 text-sm mt-1'>{{ errors.nombre }}</div>
              </div>
              
              <div>
                <label class='block text-sm font-medium text-gray-700'>Descripción</label>
                <textarea v-model='form.descripcion' rows='3' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'></textarea>
                <div v-if='errors.descripcion' class='text-red-500 text-sm mt-1'>{{ errors.descripcion }}</div>
              </div>
              
              <div>
                <label class='block text-sm font-medium text-gray-700'>Precio</label>
                <input v-model='form.precio' type='number' step='0.01' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500' required>
                <div v-if='errors.precio' class='text-red-500 text-sm mt-1'>{{ errors.precio }}</div>
              </div>
              
              <div>
                <label class='flex items-center'>
                  <input v-model='form.activo' type='checkbox' class='rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'>
                  <span class='ml-2 text-sm text-gray-700'>Activo</span>
                </label>
                <div v-if='errors.activo' class='text-red-500 text-sm mt-1'>{{ errors.activo }}</div>
              </div>
              
              <div class='flex items-center gap-4'>
                <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600' :disabled='processing'>
                  Actualizar
                </button>
                <Link :href='route(\"{$routeName}.index\")' class='text-gray-600 hover:text-gray-800'>
                  Cancelar
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  {$lowerName}: Object,
  errors: Object
})

const form = useForm({
  nombre: props.{$lowerName}.nombre,
  descripcion: props.{$lowerName}.descripcion,
  precio: props.{$lowerName}.precio,
  activo: props.{$lowerName}.activo
})

function submit() {
  form.put(route('{$routeName}.update', props.{$lowerName}.id))
}
</script>";
    }

    private function getDeletebulkVueContent(string $name): string
    {
        $routeName = Str::kebab($name);
        
        return "<template>
  <AppLayout>
    <div class='py-12'>
      <div class='max-w-7xl mx-auto sm:px-6 lg:px-8'>
        <div class='bg-white overflow-hidden shadow-xl sm:rounded-lg'>
          <div class='p-6'>
            <h2 class='text-2xl font-semibold text-gray-800 mb-6'>Eliminación Masiva de {$name}s</h2>
            
            <div class='bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6'>
              <div class='flex'>
                <div class='flex-shrink-0'>
                  <svg class='h-5 w-5 text-yellow-400' viewBox='0 0 20 20' fill='currentColor'>
                    <path fill-rule='evenodd' d='M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z' clip-rule='evenodd' />
                  </svg>
                </div>
                <div class='ml-3'>
                  <h3 class='text-sm font-medium text-yellow-800'>Atención</h3>
                  <div class='mt-2 text-sm text-yellow-700'>
                    <p>Esta acción eliminará permanentemente los {$name}s seleccionados. Esta acción no se puede deshacer.</p>
                  </div>
                </div>
              </div>
            </div>

            <form @submit.prevent='submit' class='space-y-6'>
              <div>
                <label class='block text-sm font-medium text-gray-700 mb-4'>Seleccione los {$name}s a eliminar:</label>
                <div class='space-y-2 max-h-64 overflow-y-auto border rounded-md p-4'>
                  <label v-for='item in items' :key='item.id' class='flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded'>
                    <input type='checkbox' :value='item.id' v-model='form.selected_items' class='rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500'>
                    <span class='text-sm text-gray-700'>{{ item.nombre }} - {{ item.descripcion }}</span>
                  </label>
                </div>
                <div v-if='errors.selected_items' class='text-red-500 text-sm mt-1'>{{ errors.selected_items }}</div>
              </div>
              
              <div class='flex items-center gap-4'>
                <button type='submit' class='bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700' :disabled='processing || form.selected_items.length === 0'>
                  Eliminar Seleccionados ({{ form.selected_items.length }})
                </button>
                <Link :href='route(\"{$routeName}.index\")' class='text-gray-600 hover:text-gray-800'>
                  Cancelar
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  items: Array,
  errors: Object
})

const form = useForm({
  selected_items: []
})

function submit() {
  if (form.selected_items.length === 0) {
    alert('Debe seleccionar al menos un {$name} para eliminar')
    return
  }
  
  form.delete(route('{$routeName}.destroybulk'))
}
</script>";
    }
}