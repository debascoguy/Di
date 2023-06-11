# Di - Dependency Injection

A PHP 8.0+ Simple Dependency Injection - Di - library. Light weight and Easy to use. Autowire any class. Similarly, you can autowire any config variables or values stored in the container.

 I am not going to bore you with lots of write-up to justify why you should or should not use this library as I strongly believe nobody says no to problem solving tools. So, here are the simplest example as it can be used to solve Di:

 ```php

class Hello {

    protected $value = "Hello";

    public function __toString() {
        return $this->value;
    }
}

class World {

    protected $value = "World";

    public function __toString() {
        return $this->value;
    }
}

class HelloWorld {

    /**
     * See how we use the annotation to auto-inject the dependency class
     * @var Hello
     * @Inject Hello
     * 
     * OR - use PHP 8 Attribute
     */
    #[Inject([Hello::class])]
    protected  Hello $hello;

    /**
     * @var World
     * @Inject World
     * 
     * OR - use PHP 8 Attribute
     */
    #[Inject([World::class])]
    protected  World $world;

    /**
     * auto-wire by from CONFIG_VAR registered in container
     * @var string
     * @Inject (config='firstname')
     * 
     * OR - use PHP 8 Attribute
     */
    #[Inject(['firstname'])]
    protected string $firstname;

    /**
     * auto-wire by from CONFIG_VAR registered in container
     * @var string
     * @Inject (config='lastname')
     * 
     * OR - use PHP 8 Attribute
     */
    #[Inject(['lastname'])]
    protected string $lastname;

    /**
     * auto-wire by name registered in container
     * @var string
     * @Inject (name='email')
     * 
     * OR - use PHP 8 Attribute
     */
    #[Inject(['email'])]
    protected string $email;

    public function getHello() {
        return $this->hello;
    }

    public function getWorld() {
        return $this->world;
    }

    /**
     * You can use any of the Standard inject annotation here. e.g:
     * 
     * @Inject Hello $hello
     * @Inject World $world
     * 
     * OR - use PHP 8 Attribute
     */
    #[Inject(['hello' => Hello::class, 'world' => World::class])]
    public function exampleForInjectingMethod(Hello $hello, World $world) {
        $this->hello = $hello;
        $this->world = $world;
    }

    public function __toString() {
        return $this->hello . " " . $this->world . " " . $this->firstname . " " . $this->lastname;
    }
}


use Emma\Di\Autowire\Autowire;
use ContainerManager;

/** @var AutowireInterface $autowire */
$autowire = $this->getContainer()->get(Autowire::class);

$container = $this->getContainer();
$container->register('CONFIG_VAR', ['firstname' => 'Ademola', 'lastname' => 'Aina']);
$container->register('email', 'debascoguy@gmail.com');


//All annotation with @Inject will be auto-wired into as dependency class
$helloWorld = $autowire->autowire(new HelloWorld());

//OR
$helloWorld = $autowire->autowire(HelloWorld::class);

echo $helloWorld->getHello();   //Output string "Hello"
echo $helloWorld->getWorld();   //Output string "World"
echo $helloWorld;               //Output string "Hello World Ademola Aina"

//BASIC AUTO-INJECT FUNCTION CALL
$helloWorld = new HelloWorld();

$methodsParameterMap = $callableParams = $autowire->autowire($helloWorld, "exampleForInjectingMethod");
//OR
$methodsParameterMap = $callableParams = $autowire->autowire(HelloWorld::class, "exampleForInjectingMethod");

$helloWorld->exampleForInjectingMethod($methodsParameterMap[0], $methodsParameterMap[1]);

echo $helloWorld->getHello();   //Output string "Hello"
echo $helloWorld->getWorld();   //Output string "World"
echo $helloWorld;               //Output string "Hello World Ademola Aina"



 ```

