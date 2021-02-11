
/** Функция вызова основной формы приложения **/

function show_grid(id_admission,type_admission,fio,address,login_enter){
    Ext.QuickTips.init();                           //Инициализация подсказок

    var store = new Ext.data.Store({                //Загрузка данных из БД
        baseParams:{id_admission:id_admission,type_admission:type_admission},
        proxy: new Ext.data.HttpProxy({url:'rows.php'}),
        reader: new Ext.data.JsonReader({
            root:'items',totalProperty:'totalCount'},
            [{name:'id_status'},                    //Идентификатор квитанции
            {name:'mm24'},                          //Отчетная дата
            {name:'state_gaz'},                     //Газ
            {name:'state_hvs1'},                    //ХВС 1
            {name:'state_hvs2'},                    //ХВС 2
            {name:'state_electric'},                //Электроэнергия
            {name:'summ'},                          //Сумма платежа
            {name:'isumm'}                          //Оплаченная сумма
        ])
    });
    store.load();

    var add = new Ext.FormPanel({                     //Форма добавления данных
        frame:true,
        width:350,
        items:[
        {
            xtype:'textfield',
            fieldLabel:'Газ',
            id:'gaz',                
            maskRe:/[0-9.]/,                           //Маска по вводимые символы и пробелы
            autoCreate:{tag:'input',maxLenght:'13',autocmplette:'off'}  //Ограничение ввода в 13 символов
        },{
            xtype:'textfield',
            fieldLabel:'ХВС 1',
            id:'hvs_1',                
            maskRe:/[0-9.]/,                          //Маска по вводимые символы и пробелы
            autoCreate:{tag:'input',maxLenght:'13',autocmplette:'off'}  //Ограничение ввода в 13 символов
        },{
            xtype:'textfield',
            fieldLabel:'ХВС 2',
            id:'hvs_2',                
            maskRe:/[0-9.]/,                           //Маска по вводимые символы и пробелы
            autoCreate:{tag:'input',maxLenght:'13',autocmplette:'off'}  //Ограничение ввода в 13 символов
        },{
            xtype:'textfield',
            fieldLabel:'Электроэнергия',
            id:'electro',                
            maskRe:/[0-9.]/,                          //Маска по вводимые символы и пробелы
            autoCreate:{tag:'input',maxLenght:'13',autocmplette:'off'}  //Ограничение ввода в 13 символов
        }
    
        ],
        buttons:[{
            text:'Добавить',
            icon:'img/add.png',
            handler:function(){                
                gaz=Ext.getCmp('gaz').getValue();
                hvs_1=Ext.getCmp('hvs_1').getValue();
                hvs_2=Ext.getCmp('hvs_2').getValue();
                electro=Ext.getCmp('electro').getValue();

                if(Ext.isEmpty(gaz)||Ext.isEmpty(hvs_1)||Ext.isEmpty(hvs_2)||Ext.isEmpty(electro)){
                    Ext.Msg.alert('Ошибка','Заполните все поля');
                    return;
                }

				// Добавление нового извещения
                Ext.Ajax.request({
                    url:'add.php',                    
                    params:{                       
                        gaz:gaz,
                        hvs_1:hvs_1,
                        hvs_2:hvs_2,
                        electro:electro,
                        id_admission:id_admission,
                        type_admission:type_admission
                    },
                    success:function(){
                        Ext.Msg.alert('Сообщение','Показания успешно введены!');
                        store.load();
                        Ext.getCmp('gaz').setValue('');
                        Ext.getCmp('hvs_1').setValue('');
                        Ext.getCmp('hvs_2').setValue('');
                        Ext.getCmp('electro').setValue('');
                        win_add.hide();
                    },
                    failure:function(){
                        Ext.Msg.alert('Ошибка','Внутренняя ошибка программы!');

                    }
                 });
            }
        }]
    });

	//Окно с формой добавления данных
    var win_add = new Ext.Window({                  
        title:'Добавить',
        closeAction:'hide',
        layout:'fit',
        width:300,
        height:200,       
        items:[add]
    });
	
	//Форма смены пароля пользователя
    var pass_form = new Ext.FormPanel({                     
        frame:true,
        items:[
        {
            xtype:'textfield',
            fieldLabel:'Введите текущий пароль',
            id:'old_pass',
            inputType:'password'
        },{
            xtype:'textfield',
            fieldLabel:'Введите новый пароль',
            id:'new_pass',
            inputType:'password'
        },{
            xtype:'textfield',
            fieldLabel:'Введите новый пароль еще раз',
            id:'new_pass_2',
            inputType:'password'
        }],
        buttons:[{
            text:'Сменить пароль',
            icon:'img/key_go.png',
            handler:function(){                
                old_pass=Ext.getCmp('old_pass').getValue();
                new_pass=Ext.getCmp('new_pass').getValue();
                new_pass_2=Ext.getCmp('new_pass_2').getValue();

                if(Ext.isEmpty(old_pass)||Ext.isEmpty(new_pass)||Ext.isEmpty(new_pass_2)){
                    Ext.Msg.alert('Ошибка','Заполните все поля');
                    return;
                }
				
				// Смена пароля
                Ext.Ajax.request({
                    url:'new_password.php',                    
                    params:{
                        old_pass:old_pass,
                        new_pass:new_pass,
                        new_pass_2:new_pass_2,
                        login_enter:login_enter,
                        type_admission:type_admission
                    },
                    callback:function(opts,svss,resp){
                        ff = Ext.decode(resp.responseText);
                        if (ff==1) {Ext.Msg.alert('Сообщение','Пароль успешно изменен!'); pass_new.hide();}
                        if (ff==0) {Ext.Msg.alert('Ошибка','Внутренняя ошибка программы!');}
                        if (ff==2) {Ext.Msg.alert('Ошибка','Новый пароль и его подтверждение отличаются!');}
                        if (ff==3) {Ext.Msg.alert('Ошибка','Неверно указан старый пароль!');}
                    }
                });
            }

        }]

    });
    //Форма смены пароля пользователю из под администратора
    var pass_admin_form = new Ext.FormPanel({                             
        frame:true,
        labelWidth:200,               
        items:[
        {
            xtype:'textfield',            
            fieldLabel:'Введите логин пользователя',
            allowBlank:false,
            id:'user_login'
        },{
            xtype:'textfield',
            fieldLabel:'Введите новый пароль пользователя',
            id:'pass_user_new',
            inputType:'password'
        },{
            xtype:'textfield',
            fieldLabel:'Введите новый пароль пользователя еще раз',
            id:'pass_user_new_2',
            inputType:'password'
        }],
        buttons:[{
            text:'Сменить пароль пользователю',
            icon:'img/key_go.png',
            handler:function(){
                user_login=Ext.getCmp('user_login').getValue();
                pass_user_new=Ext.getCmp('pass_user_new').getValue();
                pass_user_new_2=Ext.getCmp('pass_user_new_2').getValue();

                if(Ext.isEmpty(user_login)||Ext.isEmpty(pass_user_new)||Ext.isEmpty(pass_user_new_2)){
                    Ext.Msg.alert('Ошибка','Заполните все поля');
                    return;
                }

                // Смена пароля
                Ext.Ajax.request({
                    url:'new_password_for_user.php',                    
                    params:{
                        user_login:user_login,
                        pass_user_new:pass_user_new,
                        pass_user_new_2:pass_user_new_2,
                        type_admission:type_admission                        
                    },
                    callback:function(opts,svss,resp){
						ff = Ext.decode(resp.responseText);
                        if (ff==1) {Ext.Msg.alert('Сообщение','Пароль у пользователя '+user_login+' успешно изменен!'); pass_admin.hide();}
                        if (ff==0) {Ext.Msg.alert('Ошибка','Внутренняя ошибка программы!');}
                        if (ff==2) {Ext.Msg.alert('Ошибка','Новый пароль и его подтверждение отличаются!');}
                        if (ff==3) {Ext.Msg.alert('Ошибка','Неверно указан логин пользователя!');}
                    }
                });
            }
        }]
    });

	//Окно с формой смены пароля пользователя
    var pass_new = new Ext.Window({                  
        title:'Смена пароля',
        closeAction:'hide',
        layout:'fit',
        width:300,
        height:200,       
        items:[pass_form]
    });
    //Окно с формой смены пароля пользователю из под администратора
    var pass_admin = new Ext.Window({                  
        title:'Смена пароля пользователю',
        closeAction:'hide',
        layout:'fit',
        width:400,
        height:200,       
        items:[pass_admin_form]
    });

    var csm = new Ext.grid.CheckboxSelectionModel({singleSelect:true});

	// Таблица с данными об извещениях
    igrid = new Ext.grid.GridPanel({                
        title:'<div style="height:40px;font-size:20px;padding-top: 20px;">Добро пожаловать, '+fio+' ('+address+')</div>',        
        height:930,
        sm: csm,
        columns: [ new Ext.grid.RowNumberer({header:'№<br>п/п',width: 30}),
            {header:'Извещение №',width:130,sortable:true,dataIndex:'id_status',align:'center'},           
            {header:'Отчетная дата',width:130,sortable:true,dataIndex:'mm24',align:'center'},
            {header:'Газ (куб.м.)',width:130,sortable:true,dataIndex:'state_gaz',align:'right'},
            {header:'ХВС 1 (куб.м.)',width:140,sortable:true,dataIndex:'state_hvs1',align:'right'},
            {header:'ХВС 2 (куб.м.)',width:140,sortable:true,dataIndex:'state_hvs2',align:'right'},
            {header:'Электроэнергия (кВт*ч)',width:165,sortable:true,dataIndex:'state_electric',align:'right'},
            {header:'Сумма платежа, руб.коп.',width:180,sortable:true,dataIndex:'summ',align:'right'},
            {header:'Оплаченная сумма, руб.коп.',width:180,sortable:true,dataIndex:'isumm',align:'right'}
        ],
        store:store,
        tbar: [
            {xtype:'buttongroup',items: [{xtype:'button',text:'Добавить',icon:'img/add.png',
                handler:function(){
                    win_add.show();
                }
            }]},'-',
            {xtype:'buttongroup',items: [{xtype:'button',text:'Изменить',icon:'img/page_edit.png', handler:function(){alert('Упс! Что-то пошло не так! :(')}}]},'-',
            {xtype:'buttongroup',items: [{xtype:'button',text:'Удалить',icon:'img/delete.png', handler:function(){alert('Упс! Что-то пошло не так! :(')}}]},'-',
            {xtype:'buttongroup',items: [{xtype:'button',text:'Печать извещения',icon:'img/printer.png',
                handler:function(){
					if (!csm.getSelected()) {Ext.Msg.alert('Ошибка','Для печати извешения выберите его в таблице!'); return;}
                    w = window.open('print.php?id_status='+csm.getSelected().data.id_status+'&type_admission='+type_admission);
                }
            }]},'->',
			// Обратная связь
            {xtype:'label',text:'По всем вопросам обращаться по телефону 23-23-23',style:'color:red'},'-',
            {xtype:'buttongroup',items: [{xtype:'button',text:'Сменить пароль',icon:'img/key_go.png',
                handler:function(){
                    pass_new.show();
                }            
            }]},'-',
            {xtype:'buttongroup',hidden:'true',id:'edit_pass_user',items: [{xtype:'button',text:'Сменить пароль пользователю',icon:'img/group_key.png',
                handler:function(){
                    pass_admin.show();
                }            
            }]},{xtype:'tbseparator',hidden:'true',id:'sep'},
            {xtype:'buttongroup',items: [{
                text:'Инструкция',
                icon:'img/information.png',
                handler:function(){
                    window.open('ip.doc');
                } 
            }]},'-',
            {xtype:'buttongroup',items: [{xtype:'button',text:'Выход',icon:'img/door_out.png',
                handler:function(){
                    store.removeAll(); 
                    window.location.reload();                    
                }
            }]}
            
        ],
        renderTo: Ext.get('main')
    });    
    //Проверка на вход Администратора (если вошел администратор, то показать кнопку смены пароля любому пользователю системы)
    if(type_admission==2){
        Ext.getCmp('edit_pass_user').show();
        Ext.getCmp('sep').show();
    }
  
};