
## 🛠 Skills
Javascript, HTML, CSS...


# Shop Rest API

The store api is built with Laravel language 





![Logo](https://i.ibb.co/Cz53kN0/4347955.jpg)


## Deployment

To deploy this project run

```bash
  php artisan serve
```


## Features

- 1: Registration             
- 2: Get user information (address, phone, email)  
- 3: Connect to the payment gateway  
- 4: Classification of products   
- 5: Create multiple photos for products  
- 6: Creating a brand  
- 7: Classification of products  
- 8: And...


## Attributes


## API Reference

#### Create user

```http
  Post /api/user
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. Your Name
| `cellphone`|`string`|**Required**. Your cellphone
|`email`|`string`|**Required**. Your email
|`email_verified_at`|`string`|**Required**. Your email verified
|`password`|`string`|**Required**. Your password

#### Create province

```http
  Post /api/province
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. Your province Name

#### Create city

```http
  Post /api/city
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. Your city Name
|`province_id`|`bigint`|**Required**. Your province Id

#### Create Category

```http
  Post /api/Category
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. Your Category Name
|`parent_id`|`bigint`|**Required**. Your Category parent Id 
|`description`|`string`|**Optional**. Your Category description

#### Create Brand

```http
  Post /api/brand
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. Your Brand Name
|`display_name`|`string`|**Required**. Your Brand Display Name 

#### Create Products

```http
  Post /api/Products
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. Your Product Name
|`brand_id`|`bigint`|**Required**. Your Product Brand Id 
|`category_id`|`bigint`|**Required**. Your Products Category Id
|`price`|`int`|**Required**. Your Products price 
|`quantity`|`int`|**Required**. Your Products quantity 
|`delivery_amount`|`bigint`|**Required**. Your Products Delivery Amount 
|`description`|`string`|**Required**. Your Product Description  
|`primary_image`|`string`|**Required**. Your Product Image Display 
|`images[n]`|`string`|**Required**. Your Product thumbnails 



## 🚀 About Me
I'm Web developer with more than 2 years of work experience and a bachelor's degree in computer software engineering
Interested in learning new technologies.
During the time I was involved in several large projects, I worked on the design and implementation of the system and project configuration with the members of the user community.


## Other Common Github Profile Sections

👯‍♀️ I'm looking to collaborate on



## 🔗 Links

[![linkedin](https://img.shields.io/badge/linkedin-0A66C2?style=for-the-badge&logo=linkedin&logoColor=white)](www.linkedin.com/in/mostafaniakan)



## Feedback

If you have any feedback, please reach out to us at mostafaniakan96@gmail.com

