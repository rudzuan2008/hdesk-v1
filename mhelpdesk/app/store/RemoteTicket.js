system = mhelpdesk.view.System;
Ext.define('mhelpdesk.store.RemoteTicket', {
	extend : 'Ext.data.Store',
	requires: [
        'Ext.data.proxy.JsonP'
    ],
    config: {
        storeId : 'remoteTicketStore',
        model: "mhelpdesk.model.Ticket",
        proxy: {
            type: 'jsonp',
            callbackKey: 'callback',
            url: system.getDefaultServer() + 'remote/data_ticket.php',
            reader: {
                type: 'json',
                rootProperty: 'data',
                successProperty: 'success'
            },
	        timeout : 3000, //system.dataTimeout('heavy'), // system.getDataTimeout(),
			listeners : {
				exception : function(proxy, options, operation) {
					//wbiztech.app.fireEvent('errorData', 'remoteFeedbackStore', operation.getError());
					console.error(operation.getError());
				}
			}
		},
		listeners : {
			beforeload : function(store, operation, eOpts ) {
				//wbiztech.app.fireEvent('initData',this.getStoreId());
			}
		}
    }
});