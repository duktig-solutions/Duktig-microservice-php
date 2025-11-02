# Duktig PHP Microservice - Development documentation

## CLI Workflow

The **CLI Workflow** in Duktig PHP Microservice defines how command-line processes are executed inside the framework.  
It provides a structured way to build internal automation, background jobs, log processors, and system utilities — with a modular architecture similar to the HTTP workflow but optimized for CLI operations.

---

### Entry Point

The CLI execution starts from:

`./cli/exec.php`

This entry file initializes the environment, loads configurations, parses command-line arguments, and starts the CLI routing flow.

**Main steps:**

1. Load system bootstrap and configuration.
2. Initialize **Input** and **Output** class objects.
3. Load and parse CLI routes from `./app/config/cli-routes.php`.
4. Match the route name entered in the command line.
5. Execute defined middleware(s), if any.
6. Execute the target controller and its method.
7. Output results to console using the `Output` class.

---

### CLI Input and Output

Unlike HTTP mode where `Request` and `Response` objects are used,  
the CLI mode uses dedicated **Input** and **Output** classes.

These are located at:

```
kernel/system/classes/CLI/
│
├── Input.php
├── Output.php
└── Router.php
```

---

### CLI Input

The **Input** class is responsible for parsing all command-line arguments.

It detects:

- The **route name** (first argument after the script name)
- The **parameters** defined as `--name value` pairs
- Access to the **original arguments array**

Example command:

```bash
php ./cli/exec.php archive-log-files --days 30 --compress yes
```

When parsed:

- Route: `archive-log-files`
- Parsed arguments:  
  `{ 'days' => '30', 'compress' => 'yes' }`

The class also supports reading from STDIN for interactive commands.

---

### CLI Output

The **Output** class provides all functionality to print messages, warnings, or errors in the console.

Main methods:

- `stdout($message)` — Write standard output to the terminal.
- `stderr($message)` — Write error message and exit immediately.
- `usage()` — Display usage instructions and exit.

Example behaviors:

- **Normal output:**  
  Writes message to STDOUT and continues execution.
- **Error output:**  
  Writes message to STDERR and terminates the process.
- **Usage display:**  
  Prints usage format and exits with error code.

---

### Routing

The CLI routes are defined in:

`./app/config/cli-routes.php`

Each route defines a command name, middleware (optional), and controller.

Example route:

```php
'archive-log-files' => [
    'controller' => 'System\Logs\Archiver->process',
    'middleware' => [],
    'executeUniqueProcessLifeTime' => 10
],
```

#### Route Matching

1. The **first CLI argument** after `exec.php` is treated as the route name.
2. The router looks up this route in `cli-routes.php`.
3. If not found → Output error message via `Output->stderr()`.
4. If found → Execute middleware chain, then controller.

---

### Unique Process Execution

Each CLI route can define an optional lifetime value using:

`'executeUniqueProcessLifeTime' => <seconds>`

This ensures the same command cannot run multiple times simultaneously within the defined lifetime window.

Example:

```php
'executeUniqueProcessLifeTime' => 10
```

→ The process will not start again until 10 seconds after the previous execution.

---

### Middleware Execution

Each CLI route can define **one or multiple middleware** entries.  
Middleware methods are executed **in sequence**, before the controller.

Each middleware receives **three parameters**:

```php
middlewareMethod($input, $output, $middlewareData)
```

#### Parameters:

- `$input` — Instance of `System\CLI\Input`
- `$output` — Instance of `System\CLI\Output`
- `$middlewareData` — Shared associative array passed between all middleware and the controller

---

#### Shared `$middlewareData` Flow

The `$middlewareData` array allows middlewares to inject or modify shared data across the execution chain.

For example, one middleware can validate CLI arguments and attach processed data:

```php
$middlewareData['validated_days'] = 30;
```

Later, the controller can access this value directly:

```php
$days = $middlewareData['validated_days'];
```

All middleware modifications are cumulative and available to the controller.

---

### Controller Execution

After all middleware have executed successfully, the target controller method is called.

Each controller method receives:

```php
controllerMethod($input, $output, $middlewareData)
```

This allows the controller to:

- Read CLI arguments using `$input`
- Write messages using `$output`
- Access or modify data from `$middlewareData`

#### Notes:

- The controller can print to the console using `$output->stdout()`.
- The controller may terminate execution using `$output->stderr()` if needed.
- Unlike HTTP, there is **no permissions or caching mechanism** in CLI mode.

---

### Middleware Can Stop Execution

A middleware may output an error and terminate execution using `$output->stderr()`.  
This immediately stops the workflow — no further middleware or controller will be called.

---

### Example Workflow Summary

1. **Entry:**  
   Command starts from `./cli/exec.php`.

2. **Initialize:**  
   `Input` and `Output` classes are loaded and prepared.

3. **Routing:**  
   The system matches the entered route in `./app/config/cli-routes.php`.

4. **Unique Process Check:**  
   If defined, ensures that only one process instance runs at a time.

5. **Middleware chain:**  
   Each middleware executes sequentially and may modify `$middlewareData`.

6. **Controller execution:**  
   Controller receives `$input`, `$output`, `$middlewareData`.

7. **Output:**  
   Messages or results are written to STDOUT or STDERR.

---

### Example CLI Command

```bash
php ./cli/exec.php db-backup --env production
```

Console output example:

```
--------------------------------------------------
Starting database backup...
Backup completed successfully.
--------------------------------------------------
```

---

### CLI Workflow Diagram

```
CLI Command
      │
      ▼
 ./cli/exec.php
      │
      ▼
Initialize Input + Output
      │
      ▼
Routing (./app/config/cli-routes.php)
      │
      ├── Route not found → Output->stderr()
      │
      └── Route found
              │
              ▼
       Unique Process Check
              │
              ▼
       Middleware(s)
       ├── Validate Params → adds to $middlewareData
       ├── Prepare Environment
       └── Other custom middlewares
              │
              ▼
         Controller
         │ Receives ($input, $output, $middlewareData)
              │
              ▼
          CLI Output
              │
              ▼
          Console Display
```

---

End of document