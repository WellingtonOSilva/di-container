# Dependency Injection Container Example

This project provides a simple implementation of a Dependency Injection Container in PHP, enabling automatic service resolution and dependency injection.

## Installation

Ensure you have Composer installed. Then, install the required dependencies:
```bash 
compose require wellingtonosilva/di-container
```

## How It Works

The DI Container manages class dependencies and automatically resolves them when requested. It follows these key principles:

1. Singleton Pattern – The container ensures only one instance of each service is created.
1. Automatic Dependency Injection – It resolves dependencies recursively and injects them.
1. Provider-Based Configuration – Providers allow registering services dynamically.
1. Lazy Loading – Services are only instantiated when needed.

## Example usage

### Creating and using the Application Class

The App class serves as the main entry point to set up the DI Container and register providers. In most cases, you can use App directly without overriding it.

```php 
use WellingtonOSilva\DiContainer\App;
$app = new App();
```

### Defining Dependencies and Services

```php 
//Example 1 - Interface and Implementation
interface CounterService
{
    public function count();

}

class CounterServiceOneToTen implements CounterService
{

    public function count()
    {
        $x = 1;
        while ($x <= 10) {
            echo "Counting $x... \n";
            $x++;
        }
    }
}

class Speaker {
    public function speak() {
        echo "Dependency1 speaking";
    }
}

// Example 2 
class Talker {
    public function talk() {
        echo "Dependency2 talking";
    }
}

class ConversationService {
    private Speaker $speaker;
    private Talker $talker;

    public function __construct(Speaker $speaker, Talker $talker) {
        $this->speaker = $speaker;
        $this->talker = $talker;
    }

    public function communicate() {
        $this->speaker->speak();
        $this->talker->talk();
    }
}
```

### Creating and Registering a Provider

Providers allow services to be registered dynamically in the container.

```php 
class AppProvider implements Provider {

    public function setup(Container $container) {
        $container->register(Speaker::class, Speaker::class);
        $container->register(Talker::class, Talker::class);
        //Dependency named 'conversation' which is implemented by ConversationService
        $container->register('conversation' ConversationService::class);
        //Dependency using the interface CounterService
        $container->register(CounterService::class, CounterServiceOneToTen::class);

    }
}
```

### Registering the Provider and Running the Application

```php 
$app->withProvider(AppProvider::class);

$app->run(function (Container $container) {
    $service = $container->get('conversation'); //From name
    $service->communicate();
    $counter = $container->get(CounterService::class); //From interface
    $counter->count();
});
```

### Overriding the Application (If Needed)

In most cases, App is sufficient. However, if you need to add custom behavior, you can extend it:

```php 
class CustomApp extends App
{
    public function someStuff() {
        echo 'Custom behavior added';
    }
}

$app = new CustomApp();

$app->withProvider(AppProvider::class);

$app->run(function (Container $container) use ($app) {
    $service = $container->get('conversation'); //From name
    $service->communicate();
    $counter = $container->get(CounterService::class); //From interface
    $counter->count();
    
    $app->someStuff();
});
```

### License

This project is licensed under the MIT License.