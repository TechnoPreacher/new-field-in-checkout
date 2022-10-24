### Плагин добавления кастомного поля к чекауту V1.0

#### Задача:
Создать плагин, который:
1. будет добавлять новое поле в чекаут WooCommerce, например «Дополнительная информация»
2. Пользователь может ввести дополнительную информацию во время чекаута, эта информация должна показываться на всех страницах чекаута, быть в письме, которое отправляется на почту после совершения покупки и отображаться в ордере в админке.

### Результат:

####Страница оформления заказа:
- имеет дополнительное заполняемое юзером поле:  
 ![img.png](img/img.png)

####Страница "Заказ принят"
- тоже относится к чекауту, потому на неё выводится уже сохранённые данные из поля:  
 ![img_1.png](img/img_1.png)
- если кастомное поле не было заполнено, то строка к таблице не добавляется вообще

###Страница заказов в админке
- имеет колонку с содержимым кастомных полей:  
 ![img_2.png](img/img_2.png)

###Сообщение для пользователя
- на имеил юзера отсылается сообщения с информацией из кастомного поля:  
 ![img_3.png](img/img_3.png)