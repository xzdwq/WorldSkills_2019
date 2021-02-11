
/** Точка входа в программу **/

Ext.onReady(function(){
    Ext.QuickTips.init();                           //Инициализация подсказок
    start();
});

function start(){
	//Форма авторизации  
    var login = new Ext.FormPanel({                       
        frame:true,
        width:350,
        defaultType: 'textfield',
        items: [{
                fieldLabel:'Введите логин',
                id:'login',
                name:'login',
                allowBlank:false,
                maskRe:/[0-9A-z]/,                     //Маска по вводимые символы и пробелы
                autoCreate:{tag:'input',maxLenght:'15',autocmplette:'off'}  //Ограничение ввода в 15 символов
            },{
                fieldLabel:'Введите пароль',
				id:'password',
                name:'password',
                inputType:'password',
                maskRe:/[0-9A-z]/,                     //Маска по вводимые символы и пробелы
                autoCreate:{tag:'input',maxLenght:'15',autocmplette:'off'}  //Ограничение ввода в 15 символов
        }],
        buttons:[{
            text:'Войти',
            icon:'img/door_in.png',
            handler:function(){
				// Проверка логина и пароля
                login.getForm().submit({                //Отправляем введенные пользователем данные из формы
                    method:'post',
                    waitTitle:'Отправка данных',
                    waitMsg:'Ждите...',
                    url:'login.php',                    //Контроллер проверки логина и пароля
                    success:function(form,action){          //Если данные введены верно, то даем ответ
                        login_enter=Ext.getCmp('login').getValue();                       
                        show_grid(action.result.id_admission,action.result.type_admission,action.result.fio,action.result.address,login_enter); win.hide(); //Передаем параметры пользователя
                    },
                    failure:function(form,action){          //Если данные введены с ошибкой, то выдаем ошибку + проверка на отправку пустых полей
                        if(action.failureType=='server'){
                            Ext.Msg.alert('Ошибка', action.result.message);
                        }else{
                            Ext.Msg.alert('Ошибка', 'Введите логин и пароль');
                        }
                        Ext.getCmp('password').setValue();
                    }

                });
            }
        },{
            text:'Инструкция',
            icon:'img/information.png',
            handler:function(){
                window.open('ip.doc');
            } 
        },{
            text:'Помощь',
            icon:'img/help.png',
            handler:function(){
                Ext.Msg.show({
                    title:'Помощь',
                    msg:'По всем вопросам обращаться по телефону 23-23-23',
                    buttons:Ext.Msg.YES,                    
                    icon:Ext.MessageBox.INFO
                 })
            } 
        }]
    });

    var win = new Ext.Window({                          //Окно содержащее форму авторизации
        title:'Пожалуйста, авторизуйтесь',
        layout:'fit',    
        closable:false,
        draggable:false,
        resizable:false,
        width:300,
        height:150,
        items:[login]
    });
    win.show();
}