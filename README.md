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

# Test Programmer 
-- Dynamic Route 
-- Dynamic Controller 
-- Modular 
-- All Module in app/Systems 
-- Rest API CRUD
