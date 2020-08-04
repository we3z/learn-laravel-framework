# laravel研究

## 1. laravel的核心内容介绍

## 2. 理解laravel的基础

### 2.1 控制反转

#### 1. 常规方式

```php
class A 
{
    public function __construct()
    {
        $this->b = new B();
	}
    
    public function aMethod()
    {
        return $this->b->bMethod();
	}
}

class B 
{
	public function __construct() {}
    
    public function bMethod() {
		return 1;
    }
}

$b = (new A)->aMethod();
```

存在问题

类A 中方法需要使用 B类 中的方法，如果存在 B类 中依赖其他类，那么这样就会出现引用黑洞

#### 2. 控制反转

~~~php
/**
 * @desc 我是A类的注释
 */
class A 
{
	public function __construct(B $b)
	{
		$this->b = $b;
	}

	public function getB()
	{
		$this->b->bMethod();
	}
}

class B 
{
	public function __construct(C $c, D $d)
	{
		$this->c = $c;
		$this->d = $d;
	}

	public function bMethod()
	{
		echo "我是B类中的bMethod方法";
	}
}

class C
{
	public function __construct() {}

	public function cMethod() 
	{
		echo "我是C类中的cMethod方法";
	}
}

class D
{
	public function __construct() {}

	public function dMethod() 
	{
		echo "我是D类中的dMethod方法";
	}
}

class Ioc 
{
	protected $instances = [];

	public function __construct() {}

	public function getInstance($abstract)
	{
		// 获取类的反射信息，也就是类的所有信息
		$reflector = new \ReflectionClass($abstract);
		// 获取类的注释信息
		echo $reflector->getDocComment();
		// 获取类的构造函数信息
		$constructor = $reflector->getConstructor();
		// 获取反射类的构造函数的参数
		$dependencies = $constructor->getParameters();

		if (!$dependencies) {
			return new $abstract();
		}

		foreach ($dependencies as $dependency) {
			if (!is_null($dependency->getClass())) {
				// 这里$p[0]是C的实例化对象，$p[1]是D的实例化对象
				$p[] = $this->make($dependency->getClass()->name);
			}
		}
		// 创建一个类的新实例，给出的参数将传递到类的构造函数
		return $reflector->newInstanceArgs($p);
	}

	public function make($abstract) 
	{
		return $this->getInstance($abstract);
	}
}

$ioc = new Ioc();
$a = $ioc->make('A');
$a->getB();
~~~

#### 反射

在php运行时，拓展分析程序，导出或者提出关于类、方法、属性、参数的详细信息，包括注释。这种动态获取信息以及动态调用方法的功能称为反射API

如上代码中，A 类依赖于 B类，B类 依赖于 C类 和 D类，此时我们采用 Ioc类 来获取我们想要类的对象

通过make方法传入 想要生成的类的对象的名称，在通过 getInstance 方法，利用反射 来获取生成类的对象的依赖

例：

1. new Ioc类得到对象 $ioc，采用 $ioc->make('A'), 来获取 A类的对象

2. 通过反射 ReflectionClass('A') 来获取 类A 的反射 $reflectorA 
3. 通过 $reflectorA->getConstructor() 来获取 类A 的构造函数的反射 $constructorA 
4. 通过  $constructorA->getParameters(),获取 类A 构造函数参数的反射 $dependenciesA
5. ①如果 $dependenciesA 是空的，那表明 类A 的构造函数是没有任何参数的，那么我们直接去返回 new A(), 来返回 类A 的实例化对象
6. ②如果 $dependenciesA 不是空的，那表明 类A 的构造函数是存在参数的，那么我们要去判断这些参数是不是存在其他类的实例化对象，通过 $dependencyA->getClass() 来判断这个参数是不是对象，如果是对象，那么我们要继续获取这个对象, 并把这个对象放入数组 $objects 中，如果不是对象，那就不需要帮它实例化对象，最后可以通过  $reflectorA->newInstanceArgs($objects) 来实例化 A类 的对象;

```php
备注
public ReflectionClass::newInstanceArgs ([ array $args ] ) : object
// 创建一个类的新实例，给出的参数将传递到类的构造函数。
```

### 2.2 匿名函数

#### 1. 官方定义

匿名函数（Anonymous functions），也叫闭包函数（*closures*），允许 临时创建一个没有指定名称的函数。最经常用作回调函数（[callback](https://www.php.net/manual/zh/language.pseudo-types.php#language.types.callback)）参数的值。当然，也有其它应用的情况。

匿名函数目前是通过 [Closure](https://www.php.net/manual/zh/class.closure.php) 类来实现的。

#### 2.概念：

##### 1.回调函数：

​		是自己编写的，但不是自己调用的，提供给别人调用的函数，例如：array_walk, call_user_func_array、array_map等调用或者是你调用了一个支付接口，支付成功后他们要通知你是调用哪个方法就叫回调接口

##### 2. 匿名函数

没有名称的函数（经常当做回调函数使用）

##### 3. 闭包

A: 创建时封装周围状态的函数，即使周围的环境不存在了，闭包中的状态还会存在

B: 子函数可以使用父函数中的局部变量，这种行为就叫做闭包

C: 闭包就是能够读取其他函数内部变量的函数

D: 匿名函数+use就是闭包

1. 匿名函数只有在真正调用时才回去解析
2. 调用匿名函数的时候值需要传递function后面需要的参数

### 3.ServiceContainer和ServiceProvider

#### 1.ServiceProvider

概念：有能力提供服务的服务提供者

作用： 更加方便的管理、使用这些服务，说白了就是一个类的实例化对象，在启动之初将所有可能用的服务加载，用的时候去解析调用其中的方法

（因为服务容器实现了依赖注入，能更好的管理类之间的依赖关系）

#### 2.ServiceContainer

Container类 register方法 调用 servicProvider类中的register方法，并把这些注册的类放入$app的 bindings 属性中

bind实例化对象，同时做其他操作（比如，为自己的属性赋值，取别名）

make只去实例化对象，