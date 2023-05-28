# Di - Dependency Injection

A PHP 7.0+ Simple Dependency Injection - Di - library. Light weight and Easy to use. Autowire any class. Similarly, you can autowire any config variables or values stored in the container.

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
     */
    protected  Hello $hello;

    /**
     * @var World
     * @Inject World
     */
    protected  World $world;

    /**
     * auto-wire by from CONFIG_VAR registered in container
     * @var string
     * @Inject (config='firstname')
     */
    protected string $firstname;

    /**
     * auto-wire by from CONFIG_VAR registered in container
     * @var string
     * @Inject (config='lastname')
     */
    protected string $lastname;

    /**
     * auto-wire by name registered in container
     * @var string
     * @Inject (name='email')
     */
    protected string $email;

    public function getHello() {
        return $this->hello;
    }

    public function getWorld() {
        return $this->world;
    }

    public function exampleForInjectingFunction(Hello $hello, World $world) {
        $this->hello = $hello;
        $this->world = $world;
    }

    public function __toString() {
        return $this->hello . " " . $this->world . " " . $this->firstname . " " . $this->lastname;
    }
}

$di = new Emma\Di\DiFactory();
$di->getContainer()->register('CONFIG_VAR', ['firstname' => 'Ademola', 'lastname' => 'Aina']);
$di->getContainer()->register('email', 'debascoguy@gmail.com');

//All annotation with @Inject will be auto-wired into as dependency class
$helloWorld = $di->injectCallable(new HelloWorld());

//OR
$helloWorld = $di->injectCallable(HelloWorld::class);

echo $helloWorld->getHello();   //Output string "Hello"
echo $helloWorld->getWorld();   //Output string "World"
echo $helloWorld;               //Output string "Hello World Ademola Aina"

//AUTO-INJECT FUNCTION CALL
$helloWorld = $di->injectCallable([new HelloWorld(), "exampleForInjectingFunction"]);
//OR
$helloWorld = $di->injectCallable([HelloWorld::class, "exampleForInjectingFunction"]);

echo $helloWorld->getHello();   //Output string "Hello"
echo $helloWorld->getWorld();   //Output string "World"
echo $helloWorld;               //Output string "Hello World Ademola Aina"

 ```

