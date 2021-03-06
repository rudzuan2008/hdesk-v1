Ext.define('mhelpdesk.view.VisitorPage', {
	extend : 'Ext.Panel',
	xtype : 'visitorPage',
	requires : ['Ext.TitleBar', 'Ext.Video'],
	txtLogout : 'Exit',
	txtMenu : 'Menu',
	txtBack : 'Back',
	txtMotto : 'doc Mobile',
	txtCopyRight : '© 2016',
	txtCompany : 'Domain Oracle Sdn Bhd',
	txtSupportPhone : '+60129498134',
	txtSupportEmail : 'rudzuan@gmail.com',
	txtTitle : '<b>Visitor Check Page</b>: Use below button to execute related operation. ' +
			'These operations are restricted to visitor operations only. ' ,
	txtFooter : 'Click on the items above to execute the related operation...',		
	txtWelcome : 'Guard on Duty is ',		
	config : {
		itemId : 'visitorPage',
		layout : 'fit',
		cls : 'p-visitorpage',
		isLogin : false
	},
	initialize : function() {
		var locMod = this.getItemId() + '.initialize';
		var me = this;
		app.debugHandler('Start', '...', locMod);
		var strFooter = me.txtFooter;
		if (system.getIsLogin()) {
			if (system.getFullname()) strFooter = me.txtWelcome+' '+system.getFullname()+", "+me.txtFooter;
		}
		var imgSrc = system.getUrlPicturePath()+'images/hh.png';
		var headerPanel = Ext.create('Ext.Panel', {
			layout : {
				type : 'vbox',
				pack : 'center',
				align: 'center'
			},
			cls : 'r-main-title',
			items: [{
	            xtype: 'label',
	            html: "<img src='"+imgSrc+"' height='60px' />"
	            //height: 500,
	            //src: 'resources/images/LogoNissen.png'
	        },{
	        	xtype: 'label',
	        	style: 'font-size:.85em;font-weight:600;color: #ecce69;text-align: center;',
	        	html: 'for HARTAMAS HEIGHT'
	        },{
	        	xtype: 'label',
	        	html: me.txtTitle 
	        }]
		});
		var footerPanel = Ext.create('Ext.Panel', {
			layout : {
				type : 'vbox',
				pack : 'center',
				align: 'center'
			},
			cls : 'r-main-subtitle',
			html : 'Click on the items above to execute the related operation...'
		});
		var mainPanel = Ext.create('Ext.Panel', {
			itemId: 'mainPanel',
			cls: 'r-main-panel',
			layout : {
				type: 'vbox',
				pack: 'center',
				align: 'stretch'
			}
		});
		this.setItems([{
			xtype : 'panel',
			cls: 'r-container',
			layout : {
				type : 'vbox',
				pack : 'center',
				align : 'center'
			},
			items : [headerPanel,mainPanel,footerPanel]
		}]);
		this.callParent(arguments);
	}
});