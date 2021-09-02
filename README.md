# Test Programmer 
-- Dynamic Route 
-- Dynamic Controller 
-- Modular 
-- All Module in app/Systems 
-- Rest API CRUD

# User 
1. Admin 
    User : demo 
    Pass : demo123 
2. Staff Gudang 
    User : john_doe 
    Pass : kucingbadak
    
# Setting

Untuk rest API set link app/Http/Middleware/VerifyCsrfToken, 

rubah bagian ini : 

protected $except = [
		 'http://dev.sifseafood.co.id/system/*/*/*/rest/*' 
    ];

# Contoh Tampilan 

Tampilan Menu 

![image](https://user-images.githubusercontent.com/89963599/131812095-74dc52c8-199a-4781-9239-7d1e3544223a.png)

Barang Masuk 

![image](https://user-images.githubusercontent.com/89963599/131812258-4e729b9b-89ac-4c51-bfd8-3db4686aaa63.png)

Barang Keluar 

![image](https://user-images.githubusercontent.com/89963599/131812350-5dad21bb-fa27-416d-97bf-42075a91a791.png)


Setting Token Untuk Rest API (postman) 

1 token di set per 1 user jadi privilege mengikuti privilege user 

![image](https://user-images.githubusercontent.com/89963599/131812671-51292bbc-9d97-4440-8a48-bba0a4ffef9f.png)

