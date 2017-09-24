#RESTFul学习笔记(1)☞什么是RESTFul
****

###什么是RESTFul？
>简单来说，RESTFul是一种**面向资源的**软件架构**风格**；
>
>RESTFul风格设计可以降低开发的复杂性、提高系统的可伸缩性。

###RESTFul设计准则
>1.设计规划一个好API首先需要了解开发实际的功能，数据库设计结构。
>
>2.API应该尽可能通过抽象来分离数据与业务逻辑。

>3.API是服务器与客户端之间的一个公共契约。如果你对服务器上的API做了一个更改，并且这些更改无法向后兼容，那么你就打破了这个契约，所以你必须确保在引入新版本API的同时保持旧版本仍然可用。

>4.如果你只是简单的增加一个新的特性到API上，如资源上的一个新属性或者增加一个新的端点，你不需要增加API的版本。因为这些并不会造成向后兼容性的问题，你只需要修改文档即可。

>5.随着时间的推移，你可能声明不再支持某些旧版本的API。申明不支持一个特性并不意味着关闭或者破坏它。而是告诉客户端旧版本的API将在某个特定的时间被删除，并且建议他们使用新版本的API。

>6.一个好的RESTful API会在URL中包含版本信息。另一种比较常见的方案是在请求头里面保持版本信息。但是跟很多不同的第三方开发者一起工作后，我可以很明确的告诉你，在请求头里面包含版本信息远没有放在URL里面来的容易。


###RESTFul设计常见术语
>1.集合和资源
>>一个集合相当于一个数据库表，而一个资源该数据表的一行记录，但是往往不是绝对的。

>2.幂等：无边际效应，多次操作得到相同的结果

>3.URL段：在URL里面已斜杠分隔的内容

>4.端点：这个API在服务器上的URL


###RESTFul访问动作
######一个好的RESTful API只允许第三方调用者使用这四个半HTTP动词进行数据交互，并且在URL段里面不出现任何其他的动词。

>1.【GET】 (选择)：从服务器上获取一个具体的资源或者一个资源列表。

>2.【POST】（创建）： 在服务器上创建一个新的资源。

>3.【PUT】（更新）：以整体的方式更新服务器上的一个资源。

>4.【PATCH】（更新）：只更新服务器上一个资源的一个属性。

>5.【DELETE】（删除）：删除服务器上的一个资源。

>6.【HEAD】：获取一个资源的元数据，如数据的哈希值或最后的更新时间。

>6.【OPTIONS】：获取客户端能对资源做什么操作的信息。


###RESTFul实例展示
>1.如果你正在构建一个虚构的API来展现几个不同的动物园，每一个动物园又包含很多动物，员工和每个动物的物种，你可能会有如下的端点信息：

	https://api.example.com/v1/zoos
	https://api.example.com/v1/animals
	https://api.example.com/v1/animal_types
	https://api.example.com/v1/employees
>2.针对每一个端点来说，你可能想列出所有可行的HTTP动词和端点的组合。如下所示，
>
>3.ID表示动物园的ID， AID表示动物的ID，EID表示雇员的ID，还有ATID表示物种的ID。让文档里所有的东西都有一个关键字是一个好主意。
	
	GET /zoos: List all Zoos (ID and Name, not too much detail)
	POST /zoos: Create a new Zoo
	GET /zoos/ZID: Retrieve an entire Zoo object
	PUT /zoos/ZID: Update a Zoo (entire object)
	PATCH /zoos/ZID: Update a Zoo (partial object)
	DELETE /zoos/ZID: Delete a Zoo
	GET /zoos/ZID/animals: Retrieve a listing of Animals (ID and Name).
	GET /animals: List all Animals (ID and Name).
	POST /animals: Create a new Animal
	GET /animals/AID: Retrieve an Animal object
	PUT /animals/AID: Update an Animal (entire object)
	PATCH /animals/AID: Update an Animal (partial object)
	GET /animal_types: Retrieve a listing (ID and Name) of all Animal Types
	GET /animal_types/ATID: Retrieve an entire Animal Type object
	GET /employees: Retrieve an entire list of Employees
	GET /employees/EID: Retreive a specific Employee
	GET /zoos/ZID/employees: Retrieve a listing of Employees (ID and Name) who work at this Zoo
	POST /employees: Create a new Employee
	POST /zoos/ZID/employees: Hire an Employee at a specific Zoo
	DELETE /zoos/ZID/employees/EID: Fire an Employee from a specific Zoo

###RESTFul过滤器使用

>1.使用过滤器的最重要的一个原因是可以**最小化网络传输**，并让客户端尽可能快的得到查询结果。
>
>2.在客户端的角度来看，过滤器减少服务器响应请求的负载。

>3.过滤器是最有效的方式去处理那些获取资源集合的请求。所以只要出现GET的请求，就应该通过URL来过滤信息。以下有一些过滤器的例子，可能是你想要填加到API中的：

	?limit=10: 减少返回给客户端的结果数量（用于分页）
	?offset=10: 发送一堆信息给客户端（用于分页）
	?animal_type_id=1: 使用条件匹配来过滤记录
	?sortby=name&order=asc:  对结果按特定属性进行排序

>4.有些过滤器可能会与端点URL的效果重复。例如我之前提到的GET /zoo/ZID/animals。它也同样可以通过GET /animals?zoo_id=ZID来实现。独立的端点会让客户端更好过一些，因为他们的需求往往超出你的预期。

###RESTFul状态码设计
>返回API操作的响应信息，客户端根据返回状态码进行不同的处理操作：

	200 OK – [GET]
	客户端向服务器请求数据，服务器成功找到它们

	201 CREATED – [POST/PUT/PATCH]
	客户端向服务器提供数据，服务器根据要求创建了一个资源

	204 NO CONTENT – [DELETE]
	客户端要求服务器删除一个资源，服务器删除成功

	400 INVALID REQUEST – [POST/PUT/PATCH]
	客户端向服务器提供了不正确的数据，服务器什么也没做

	404 NOT FOUND – [*]
	客户端引用了一个不存在的资源或集合，服务器什么也没做

	500 INTERNAL SERVER ERROR – [*]
	服务器发生内部错误，客户端无法得知结果，即便请求已经处理成功

>状态码归类

	1xx范围的状态码是保留给底层HTTP功能使用的，并且估计在你的职业生涯里面也用不着手动发送这样一个状态码出来。
	
	2xx范围的状态码是保留给成功消息使用的，你尽可能的确保服务器总发送这些状态码给用户。
	
	3xx范围的状态码是保留给重定向用的。大多数的API不会太常使用这类状态码，但是在新的超媒体样式的API中会使用更多一些。
	
	4xx范围的状态码是保留给客户端错误用的。例如，客户端提供了一些错误的数据或请求了不存在的内容。这些请求应该是幂等的，不会改变任何服务器的状态。
	
	5xx范围的状态码是保留给服务器端错误用的。这些错误常常是从底层的函数抛出来的，并且开发人员也通常没法处理。发送这类状态码的目的是确保客户端能得到一些响应。

###RESTFul返回信息设计
>当使用不同的HTTP动词向服务器请求时，客户端需要在返回结果里面拿到一系列的信息。下面的列表是非常经典的RESTful API定义：

	GET /collection: 返回一系列资源对象

	GET /collection/resource: 返回单独的资源对象

	POST /collection: 返回新创建的资源对象

	PUT /collection/resource: 返回完整的资源对象

	PATCH /collection/resource: 返回完整的资源对象

	DELETE /collection/resource: 返回一个空文档