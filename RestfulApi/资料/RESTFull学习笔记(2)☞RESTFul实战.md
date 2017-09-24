#RESTFull学习笔记(2)☞RESTFul实战
****

###功能需求
>用户模块：用户登录和用户注册功能。

>文章模块：编写文章、修改文章、删除文章和获取文章信息功能。

###RESTFul架构设计
>根据以上功能需求，可以把所有URL请求先归到入口文件(index.php)，然后入口文件根据不同资源类型，来请求不同的功能接口。整体目录结构如下：
>
	lib
		db.php--------数据库连接文件
		ErrorCode.php--------错误码定义文件
		Article.php--------文章模块接口
		User.php--------用户据模块接口
	restful
		.htaccess--------请求重定向文件
		index.php--------请求入口文件

>所有请求根据`.htaccess`文件定义规则都会重定向到`index.php`文件，代码文件如下：
>
	RewriteEngine on
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule ^(.*)$ http://127.0.0.1/RestfulApi/restful/index.php/$1 [L]

###index.php大体结构
>1.首先定义一个Restful对象，该对象变量如下：

		/**
		 * User功能模块
		 */
		private $_user;
	
		/**
		 * Article功能模块
		 */
		private $_article;
	
		/**
		 * 请求方法
		 */
		private $_requestMethod;
	
		/**
		 * 资源名称
		 */
		private $_resourceName;
	
		/**
		 * 请求ID
		 */
		private $_id;
	
		/**
		 * 允许请求的资源列表
		 */
		private $_allowResources = ['Users','Articles'];
	
		/**
		 * 允许请求的方法
		 */
		private $_allowRequestMethods = ['GET','POST','PUT','DELETE','OPTIONS'];
	
		/**
		 * http请求状态码
		 */
		private $_statusCodes = [
			200 => 'OK',
			204 => 'No Content',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			500 => 'Server Internet Error'
	 	];

>2.构造方法如下：

	/**
	 * 构造方法
	 */
	public function __construct(User $_user,Article $_article){
		$this->_user 	= $_user;
		$this->_article = $_article;
	}

>3.入口执行方式如下：

	$user = new User($pdo);
	$article = new Article($pdo);
	$restful = new Restful($user,$article);
	$restful->run();

>4.`run()`方法接口
>>`run()`方法是分析当前"请求方法"和"请求资源"，再指向不同方法接口。

	public function run(){
		try{
			$this->_setupRequestMethod();
			$this->_setupResource();
			$this->_setId();
			if($this->_resourceName == 'Users'){
				return $this->_json($this->_handleUser());
			}else{
				return $this->_json($this->_handleArticle());
			}
		}catch(Exception $e){
			$this->_json(['error'=>$e->getMessage()],$e->getCode());
		}

	}

>>4.1`_setupRequestMethod()`初始化请求方法
>>>1.通过`$_SERVER['REQUEST_METHOD']`服务器变量获取请求方式是"GET"、"POST"、"PUT"、"OPTIONS"、"DELETE"中的哪一种。

	private function _setupRequestMethod(){
		$this->_requestMethod = $_SERVER['REQUEST_METHOD'];
		if(!in_array($this->_requestMethod, $this->_allowRequestMethods)){
			throw new Exception("请求方法不被允许", 405);
		}
	}

>>4.2`_setupResource`初始化请求资源
>>>1.通过`$_SERVER['PATH_INFO']`服务器变量获取请求的资源类型具体是哪一个；比如请求URL为`http://127.0.0.1/RestfulApi/restful/index.php/article`，则打印处的变量是`'PATH_INFO' => string '/article'`。

	private function _setupResource(){
		$path = $_SERVER['PATH_INFO'];
		$params = explode('/',$path);
		$this->_resourceName = $params[1];
		if(!in_array($this->_resourceName, $this->_allowResources)){
			throw new Exception("请求资源不被允许", 405);
		}
		if(!empty($params[2])){
			$this->_id = $params[2];
		}
	}

>>4.3通过资源类型判断在分别请求已经实例化好的模块对象中的方法；总体调度在`run()`中，以下是其中部分代码：

		if($this->_resourceName == 'Users'){
			return $this->_json($this->_handleUser());
		}else{
			return $this->_json($this->_handleArticle());
		}

###总结
>RESTFull API大概就了解到这里了，具体代码可以访问下面URL：`https://github.com/silence-ice/PHPModule/blob/master/RestfulApi

