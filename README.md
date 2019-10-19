## DOMJudge 上传扩展

之前校赛用的。因为 DOMJudge 默认的文件上传方式不太方便，所以改了改前端代码，实现了通过文本框直接上传的功能。

文件方式上传             | 直接上传 
:-------------------------:|:-------------------------:
![](https://raw.githubusercontent.com/UUNNII/DOMJudge-Upload-Helper/master/screenshot/1.png)  |  ![](https://raw.githubusercontent.com/UUNNII/DOMJudge-Upload-Helper/master/screenshot/2.png)

提交使用的还是之前的接口 `/team/upload.php`，在前端 JS 代码中拼接出请求，相当于上传了一个名为 codeRaw.[cpp/java/py...] 的文件。

在 DOMJudge 6.0.3 上测试通过，按道理其他版本应该也能使用。

### 使用步骤

1. 安装 DOMJudge。

2. 复制 **submit1.php** 到 **[domserver 安装目录]/www/team/** 下。

3. 修改 **[domserver 安装目录]/www/team/menu.php**，将所有 `submit.php` 替换为 `submit1.php`

### Installation

1. Install DOMJudge.
2. Copy **submit1.php** to **[domserver Installation directory]/www/team/submit1.php**.
3. Edit **[domserver Installation directory]/www/team/menu.php**, replace `submit.php` with `submit1.php`