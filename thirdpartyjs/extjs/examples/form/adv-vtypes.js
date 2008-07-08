/*
 * Ext JS Library 2.1
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */

// Add the additional 'advanced' VTypes
Ext.apply(Ext.form.VTypes, {
  daterange: function(val, field) {
    var date = field.parseDate(val);
    
    // We need to force the picker to update values to recaluate the disabled dates display
    var dispUpd = function(picker) {
      var ad = picker.activeDate;
      picker.activeDate = null;
      picker.update(ad);
    };
    
    if (field.startDateField) {
      var sd = Ext.getCmp(field.startDateField);
      sd.maxValue = date;
      if (sd.menu && sd.menu.picker) {
        sd.menu.picker.maxDate = date;
        dispUpd(sd.menu.picker);
      }
    } else if (field.endDateField) {
      var ed = Ext.getCmp(field.endDateField);
      ed.minValue = date;
      if (ed.menu && ed.menu.picker) {
        ed.menu.picker.minDate = date;
        dispUpd(ed.menu.picker);
      }
    }
    /* Always return true since we're only using this vtype
     * to set the min/max allowed values (these are tested
     * for after the vtype test)
     */
    return true;
  },
  
  password: function(val, field) {
    if (field.initialPassField) {
      var pwd = Ext.getCmp(field.initialPassField);
      return (val == pwd.getValue());
    }
    return true;
  },
  
  passwordText: 'Passwords do not match'
});


Ext.onReady(function(){

    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    var bd = Ext.getBody();

		/*
		 * ================  Date Range  =======================
		 */
    
    var dr = new Ext.FormPanel({
      labelWidth: 125,
      frame: true,
      title: 'Date Range',
	  bodyStyle:'padding:5px 5px 0',
	  width: 350,
      defaults: {width: 175},
      defaultType: 'datefield',
      items: [{
        fieldLabel: 'Start Date',
        name: 'startdt',
        id: 'startdt',
        vtype: 'daterange',
        endDateField: 'enddt' // id of the end date field
      },{
        fieldLabel: 'End Date',
        name: 'enddt',
        id: 'enddt',
        vtype: 'daterange',
        startDateField: 'startdt' // id of the start date field
      }]
    });

    dr.render('dr');
    
    /*
     * ================  Password Verification =======================
     */
        
    var pwd = new Ext.FormPanel({
      labelWidth: 125,
      frame: true,
      title: 'Password Verification',
      bodyStyle:'padding:5px 5px 0',
      width: 350,
      defaults: {
        width: 175,
        inputType: 'password'
      },
      defaultType: 'textfield',
      items: [{
        fieldLabel: 'Password',
        name: 'pass',
        id: 'pass'
      },{
        fieldLabel: 'Confirm Password',
        name: 'pass-cfrm',
        vtype: 'password',
        initialPassField: 'pass' // id of the initial password field
      }]
    });

    pwd.render('pw');
});