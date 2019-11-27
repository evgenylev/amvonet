Дамп БД в файле gsmtulmg.sql.

В проекте использованы FOSRestBundle + JMSSerializer.

Логика обработки транзакицй простейшая, реализовано в
хранимой процедуре makeTransaction. Сам баланс хранится
в таблице Users. Такое решение простейшее и "не далеко не 
самое правильное".

Вся логика в контроллере App\Contollers\UsersController.php 

