
# **论坛网站**

## **简介**
这是一个功能完善的论坛网站，支持用户注册、发帖和讨论功能，同时具备渐进式网页应用（PWA）特性，提供良好的移动设备支持与离线访问功能。

**演示：** [https://www.bplewiki.top](https://www.bplewiki.top)
---

## **功能特性**

### **用户功能**
- 用户注册、登录和找回密码。
- 修改密码及个人资料。

### **论坛功能**
- 支持多主题讨论。
- 发帖、回复、点赞等交互功能。
- 帖子管理，包括编辑与删除功能。

### **内容管理**
- 支持 Markdown 编辑。
- 提供代码高亮功能，适合技术讨论。

### **PWA 功能**
- 支持离线访问。
- 提供“添加到主屏幕”功能。

### **文件管理**
- 文件上传与云存储支持。

### **轻量级**
- 占用资源少。

---

## **技术栈**

### 后端
- **PHP**：用于后端开发。
- **MySQL**：存储论坛数据。
- **Monolog**：用于日志管理。

### 前端
- **HTML/CSS/JavaScript**：搭建动态页面。
- **FontAwesome**：用于美观的图标显示。
- **highlight.js**：代码高亮功能。
- **marked.js**：Markdown 解析与渲染。

### PWA
- **Service Worker**：实现离线缓存与网络拦截。
- **manifest.json**：应用元数据配置。
- **HTTPS**：保证安全性，支持 PWA 功能。

---

## **安装与部署**

### 前置要求
- Web 服务器：建议 Apache 或 Nginx。
- PHP 7.4+。
- MySQL 数据库。
- HTTPS 支持。

### 部署步骤
1. **克隆代码仓库**
   ```bash
   git clone https://github.com/your-repo.git
   cd your-repo
   ```

2. **配置数据库**
   - 配置 `/php/connect.php` 文件，填写数据库连接信息：
     ```php
     <?php
     $servername = "localhost";
     $username = "your_username";
     $password = "your_password";
     $dbname = "your_database";

     // 创建数据库连接
     $conn = new mysqli($servername, $username, $password, $dbname);

     // 检查连接
     if ($conn->connect_error) {
         die("连接失败: " . $conn->connect_error);
     }
     ?>
     ```

3. **部署到服务器**
   - 将代码上传到 Web 服务器的根目录。
   - 配置 HTTPS 支持。

4. **测试运行**
   - 打开浏览器访问网站，测试功能是否正常。

---

## **使用指南**

### 注册与登录
- 注册新用户后登录论坛。
- 用户可发帖、评论并参与讨论。

### 管理功能
- 管理员可以管理用户的封禁

---

## **许可证**
本项目采用 [MIT License](https://opensource.org/licenses/MIT)。

---

## **联系我们**
- **邮箱** thirstywaterx@outlook.com
