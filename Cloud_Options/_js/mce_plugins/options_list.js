(function() {
		var DOM = tinymce.DOM;	
        tinymce.create('tinymce.plugins.options_list', {
                /**
                 * Initializes the plugin, this will be executed after the plugin has been created.
                 * This call is done before the editor instance has finished it's initialization so use the onInit event
                 * of the editor instance to intercept that event.
                 *
                 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
                 * @param {string} url Absolute URL to where the plugin is located.
                 */
                init : function(ed, url) {                
	                	var dom = ed.dom;
	                	var options ; 
	                	jQuery.ajax({
	                		url : 'admin-ajax.php',
	                		data : {
	                			action : 'mce_get_options_list'
	                		},
	                		dataType : 'json',
	                		success : function(response ){
	                			console.log( response ); 
	                		}
	                	});
                        // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceOptions_list');
                        ed.addCommand('mceOptions_list', function() {
							dropdown = ed.windowManager.open({
								file : url + '/options_list_popup.php',
							    width : 500,
								inline: 1
							}, {
								plugin_url : url							
							});
                        });

                        // Register Options_list button
                        ed.addButton('options_list', {
                                title : 'options_list.desc',
                                cmd : 'mceOptions_list',
                                image : url + '/img/options_list.png'
                        });
                        

                        // Add a node change handler, selects the button in the UI when a image is selected
                        ed.onNodeChange.add(function(ed, cm, n) {
                             cm.setActive('options_list', n.nodeName == 'IMG');
                        });  
                },             

                /**
                 * Creates control instances based in the incomming name. This method is normally not
                 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
                 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
                 * method can be used to create those.
                 *
                 * @param {String} n Name of the control to create.f
                 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
                 * @return {tinymce.ui.Control} New control instance or null if no control was created.
                 */
                createControl : function(n, cm) {
                        return null;
                },

                /**
                 * Returns information about the plugin as a name/value array.
                 * The current keys are longname, author, authorurl, infourl and version.
                 *
                 * @return {Object} Name/value array containing information about the plugin.
                 */
                getInfo : function() {
                        return {
                                longname : 'options_list plugin',
                                author : 'Cloud',
                                authorurl : 'http://cloudwebteam.com',
                                version : "1.0"
                        };
                }
        });

        // Register plugin
        tinymce.PluginManager.add('options_list', tinymce.plugins.options_list);
})();