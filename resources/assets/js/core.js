$(document).ready(function() {
    $.fn.autoResize = function(options) {

        // Just some abstracted details,
        // to make plugin users happy:
        var settings = $.extend({
            onResize : function(){},
            animate : true,
            animateDuration : 150,
            animateCallback : function(){},
            extraSpace : 20,
            limit: 1000
        }, options);

        // Only textarea's auto-resize:
        this.filter('textarea').each(function(){

            // Get rid of scrollbars and disable WebKit resizing:
            var textarea = $(this).css({resize:'none','overflow-y':'hidden'}),

                // Cache original height, for use later:
                origHeight = textarea.height(),

                // Need clone of textarea, hidden off screen:
                clone = (function(){

                    // Properties which may effect space taken up by chracters:
                    var props = ['height','width','lineHeight','textDecoration','letterSpacing'],
                        propOb = {};

                    // Create object of styles to apply:
                    $.each(props, function(i, prop){
                        propOb[prop] = textarea.css(prop);
                    });

                    // Clone the actual textarea removing unique properties
                    // and insert before original textarea:
                    return textarea.clone().removeAttr('id').removeAttr('name').css({
                        position: 'absolute',
                        top: 0,
                        left: -9999
                    }).css(propOb).attr('tabIndex','-1').insertBefore(textarea);

                })(),
                lastScrollTop = null,
                updateSize = function() {

                    // Prepare the clone:
                    clone.height(0).val($(this).val()).scrollTop(10000);

                    // Find the height of text:
                    var scrollTop = Math.max(clone.scrollTop(), origHeight) + settings.extraSpace,
                        toChange = $(this).add(clone);

                    // Don't do anything if scrollTip hasen't changed:
                    if (lastScrollTop === scrollTop) { return; }
                    lastScrollTop = scrollTop;

                    // Check for limit:
                    if ( scrollTop >= settings.limit ) {
                        $(this).css('overflow-y','');
                        return;
                    }
                    // Fire off callback:
                    settings.onResize.call(this);

                    // Either animate or directly apply height:
                    settings.animate && textarea.css('display') === 'block' ?
                        toChange.stop().animate({height:scrollTop}, settings.animateDuration, settings.animateCallback)
                        : toChange.height(scrollTop);
                };

            // Bind namespaced handlers to appropriate events:
            textarea
                .unbind('.dynSiz')
                .bind('keyup.dynSiz', updateSize)
                .bind('keydown.dynSiz', updateSize)
                .bind('change.dynSiz', updateSize);

        });

        // Chain:
        return this;

    };

    var toolTips = $('[data-toggle="tooltip"]');
    if(toolTips.length) {
        toolTips.tooltip({html: true});
	}

	$.fn.dataTable.moment( 'MM/DD/YYYY' );
	$.fn.dataTable.moment( 'MM/DD/YYYY HH:mm:ss' );

	var dataTable = $('.datatable');
	if(dataTable.length) {

		var loadin = $('.loadin');
		dataTable.DataTable({
			"lengthMenu": [[25, 50, 100, 250, 500, -1], [25, 50, 100, 250, "All"]],
			stateSave: true,

			"aaSorting": [
				[ $('.datatable thead th.sort_by').index('.datatable thead th'), $('.datatable thead th.sort_by').attr('data-sort_order') ]
			],
			"fnPreDrawCallback": function () {
				dataTable.hide();
				loadin.show();
			},
			"fnDrawCallback": function () {
				dataTable.show();
				loadin.hide();
			},
			"fnInitComplete": function () {
				dataTable.show();
				loadin.hide();
			}
		});
	}

	$('.clean').keypress(function(e) {
		var regex = new RegExp("^[a-zA-Z0-9-_]+$");
		var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
		if (regex.test(str)) {
			return true;
		}
		e.preventDefault();
		return false;
	});

	$('.numonly').keypress(function(e) {
		var regex = new RegExp("^[0-9]+$");
		var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
		if (regex.test(str)) {
			return true;
		}
		e.preventDefault();
		return false;
	});

	var colorField = $('input#color');
	if (colorField.length) {
		colorField.spectrum({
			showPalette:true,
			showInput: true,
			color: colorField.val(),
			palette: [
				['#61bd4f', '#f2d600', '#ff9f1a', '#d8242e', '#c377e0'],
				['#0079bf', '#00c2e0', '#51e898', '#ff78cb', '#355263']
			]
		});
	}

	$('input.colorpick').spectrum({
		showInput: true,
		showPalette: true,
//		color: colorF.val(),
		preferredFormat: "hex",
		appendTo: "#addHT"
	});

	var contactType = $('#contact-type');
	if(contactType.length) {
		contactType.each(function() {
            $(this).selectize({
                persist: false,
                maxItems: null,
                options: $(this).data('options'),
                create: function(input) {
                    return {text: input, value: input};
                }
            });
		});
	}

	$('#frm').on('submit', function(e) {
		$('#add_btn').html('<i class="fa fa-circle-o-notch fa-spin"></i> Doing something awesome..');
		// $('#add_server_btn').attr('disabled', true);
	});

	$('button#edit_lead_btn').on('click', function(e) {
		$('#edit_lead_btn').html('<i class="fa fa-circle-o-notch fa-spin"></i> Reprocessing..');
	});

	$('button#add_lead_btn').on('click', function(e) {
		$('#add_lead_btn').html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing..');
	});

	$('#delete_btn').on('click', function() {
		var conf = confirm('Are you sure you want to delete this?');
		if (conf) {
			var href = $(this).attr('data-href');
			window.location.href = href;
		}
	});

	$('#duplicate_btn').on('click', function() {
		var name = prompt('What is the new repo name?');
		if (name) {
			var href = $(this).attr('data-href') + name;
			window.location.href = href;
		}
	});

	$('.frm').on('submit', function(e) {
		$('.add_btn').html('<i class="fa fa-circle-o-notch fa-spin"></i> Doing something awesome..');
		// $('#add_server_btn').attr('disabled', true);
	});

	$('.reboot').on('click', function() {
		var conf = confirm('Are you sure you want to reboot this server?');
		if (conf) {
			var href = $(this).attr('data-href');
			window.location.href = href;
		}
	});

	$('.node_add_key').on('click', function() {
		var node_ip = $(this).attr('data-node_ip');
		if (node_ip) {
			$('#node_ip').val(node_ip);
			jQuery('#addNodeKey').modal('show', {backdrop: 'static'});
		}
	});

	$('#bb_source').on('change', function() {
		var bb_source = $(this).val();
		$.ajax({
			url: "/tools/ajax_plugins_by_bbsource?bb_source="+bb_source,
			dataType: "JSON",
			success: function(json){
				var cnt = 0;

				$('.plugins_list').html("");

				for (var i=0;i<json.length;i++) {
					var chk = '<div class="col-sm-6 checkbox"><label><input name="selected_plugins[]" value="' + json[i].plugin_id + '" type="checkbox"> ' + json[i].plugin_name + '</label></div>';
					$('.plugins_list').append(chk);
					cnt++;
				}
				if (cnt > 0) {
					$('#plugins_block').fadeIn();
				} else {
					$('.plugins_list').empty();
					$('#plugins_block').hide();
				}
			}
		})
	});

	$('.ajax_client_id_convo').on('change', function() {
		var client_id = $(this).val();
		$.ajax({
			url: "/tools/ajax_ms_by_client?client_id="+client_id,
			dataType: "JSON",
			success: function(json){

				$('#messaging_services').html('<option value="-1">--- All Services ---</option>');

				var cnt = 0;
				for (var i=0;i<json.length;i++) {
					$('#messaging_services').append('<option value="' + json[i].ms_id + '">' + json[i].ms_name + ' (' + json[i].ms_sid + ')</option>');
					cnt++;
				}
			}
		})
	});

    var credentialList = $('.credential-list');


    function credMsg(response) {
        var credMsgBox = credentialList.find('.msg-box');
        credMsgBox.slideDown();
        if (response.success) {
            credMsgBox.addClass('alert-success').removeClass('alert-error');
        } else {
            credMsgBox.addClass('alert-error').removeClass('alert-success');
        }
        credMsgBox.text(response.message);

        setTimeout(function () {
            credMsgBox.slideUp();
        }, 4000);
    }

    if(credentialList.length) {
        var credList = document.getElementById('credential-list'),
            credSortData = [];

        var credSort = Sortable.create(credList, {
            handle: '.fa-arrows',
            draggable: 'tr',
            onUpdate: function (evt) {
                var rows = credList.querySelectorAll('tr');
                for (var i = 0; i < rows.length; i++) {
                    credSortData.push(rows[i].dataset.id);
                }

                $.ajax({
                    url: '/client-credentials/update-sort',
                    data: {ids: credSortData, _token: window.Laravel.csrfToken},
                    type: 'post',
                    dataType: 'json',
                    success: credMsg
                });
            }
        });
    }

    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-pills a[href="#' + url.split('#')[1] + '"]').tab('show');
    }

    $(document).on('click', '#note-list a[href*="#"]', function() {
    	var href = $(this).attr('href');
    	window.location = href.replace('#', '?'+Date.now()+'#');
    	return false;

	}).on('click', '.credential-list .edit-btn', function() {
    	var row = $(this).closest('tr');
    	row.toggleClass('active').find('input,textarea').prop('disabled', !row.hasClass('active'));
    	row.find('textarea').autoResize();
    	$(this).find('i.fa').toggleClass('fa-edit fa-undo');
    	$(this).prev('.save-btn').toggle();

	}).on('click', '.credential-list .save-btn', function() {
        var saveBtn = $(this),
			row = saveBtn.closest('tr'),
			label = row.find('input').val(),
			value = row.find('textarea').val();

        $.ajax({
			url: '/client-credentials/update/'+row.data('id'),
			data: {label: label, client_id: $(this).data('client'), value: value, _token: window.Laravel.csrfToken},
			type: 'post',
			dataType: 'json',
			success: credMsg,
			complete: function() {
                saveBtn.hide();
                saveBtn.siblings('.edit-btn').trigger('click');
			}
		});

	}).on('click', '.credential-list .delete-btn', function() {
        if(confirm('Are you sure you want to delete the "'+$(this).data('title')+'" credential?')) {
            var row = $(this).closest('tr');
            $.ajax({
                url: '/client-credentials/delete/' + $(this).data('id'),
                data: {cred_type: $(this).data('type'), client_id: $(this).data('client'), _token: window.Laravel.csrfToken},
                type: 'post',
                dataType: 'json',
                success: credMsg,
                complete: function() {
                    row.remove();
                }
            });
        }

    }).on('click', '.note-list .delete-btn', function() {
        if(confirm('Are you sure you want to delete this note?')) {
            var row = $(this).closest('tr');
            $.ajax({
                url: '/client-notes/delete/' + $(this).data('id'),
                data: {client_id: $(this).data('client'), _token: window.Laravel.csrfToken},
                type: 'post',
                dataType: 'json',
                success: function(response) {
                    var noteMsgBox = $('.note-list').find('.msg-box');
                    noteMsgBox.slideDown();
                    if (response.success) {
                        noteMsgBox.addClass('alert-success').removeClass('alert-error');
                    } else {
                        noteMsgBox.addClass('alert-error').removeClass('alert-success');
                    }
                    noteMsgBox.text(response.message);

                    setTimeout(function () {
                        noteMsgBox.slideUp();
                    }, 4000);
                },
                complete: function() {
                    row.remove();
                }
            });
        }
        return false;

    }).on('click', 'a.delete-link', function() {
        return confirm('Are you sure you want to delete: ' + $(this).data('title') + '? This will also remove it from any clients.');

	}).on('click', '.modal-checkbox-field .btn-primary', function() {
		var modalField = $(this).closest('.modal'),
			resultField = $('.select-multiple-results[data-target="#'+modalField.attr('id')+'"]');

		resultField.html('');
		modalField.find('input[type="checkbox"]:checked').each(function() {
			var id = $(this).attr('value'),
				color = $(this).data('color'),
				text = $(this).next('span').text();

			resultField.append('<span style="background-color: '+color+'">'+ text +'<button class="remove-cat" data-id="'+ id +'"><i class="fa fa-close"></i></button></span>');
		});
		modalField.modal('hide');

	}).on('click', '.select-multiple-results .remove-cat', function() {
		var id = $(this).data('id'),
			modalField = $($(this).closest('.select-multiple-results').data('target'));

		modalField.find('input[type="checkbox"]:checked').each(function() {
			if($(this).attr('value') == id) {
				$(this).prop('checked', false);
			}
		});
		$(this).closest('span').remove();

	}).on('hidden.bs.modal', '.modal-checkbox-field', function(e) {
		var id = $(this).attr('id'),
			cbx = $(this).find('input[type="checkbox"]'),
			catIds = [];

		$('.select-multiple-results[data-target="#'+ id +'"]').find('span').each(function() {
			catIds.push($(this).find('button').data('id').toString());
		});

        cbx.each(function() {
            if(catIds.indexOf($(this).attr('value')) > -1) {
                $(this).prop('checked', true);
            } else {
            	$(this).prop('checked', false);
			}
        });

	}).on('change', 'input.tab-radio', function() {
		$(this).tab('show');

	}).on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;

        if(e.target.hash === '#credentials') {
            var txtArs = credentialList.find('textarea');

            setTimeout(function() {
                txtArs.each(function() {
                    $(this).height($(this)[0].scrollHeight);
                })
            },300);
		}

    }).on('change', 'select.node_client_id', function() {
    	var sel = $(this),
			val = sel.val(),
			id = sel.data('id');

    	if(val === 'none') {
    		val = null;
        }
        sel.siblings('i.fa').remove();
		sel.after('<i class="fa fa-circle-o-notch fa-pulse"></i>');

		$.ajax({
			url: '/nodes/update-node-client/'+id,
			type: 'post',
			data: {client_id: val, _token: window.Laravel.csrfToken},
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					sel.siblings('i.fa').toggleClass('fa-circle-o-notch fa-pulse fa-check success');
					console.log(response.message);
				} else {
					sel.siblings('i.fa').toggleClass('fa-circle-o-notch fa-pulse fa-close error');
					console.log(response.message);
				}
                setTimeout(function() {
                    sel.siblings('i.fa').remove();
                }, 5000);
			}
		});
	}).on('show.bs.modal', '.client-contact-modal', function(e) {
		$(this).find('#contact-type').selectize({
            persist: false,
            maxItems: null,
            options: $(this).data('options'),
            create: function(input) {
                return {text: input, value: input};
            }
        });
	});
});


(() => {
    let addHtForm = document.querySelector('.add-hypertargeting-form');
    if (addHtForm) {
        addHtForm.querySelector('#hypertargeting_template_id').addEventListener('change', handleTemplateSelectionChange);
    }
    let addHtButton = document.querySelector('[data-target="#addHT"]');
    console.log(addHtButton);
    if (addHtButton) {
        addHtButton.addEventListener('click', handleModalShown);
    }

    function getAllowedFields(id) {
        return fetch ('/api/hypertargeting/allowed_fields/' + id).then((response) => {
            return response.json();
        });
    }

    function hideFormGroupOfField(field) {
        field.closest('.form-group').classList.add('hidden');
    }

    function showFormGroupOfField(field) {
        field.closest('.form-group').classList.remove('hidden');
    }

    function hideAllChildFields(form) {
        form.querySelectorAll('.form-control, input[type="file"]').forEach((item) => {
            hideFormGroupOfField(item);
        });
    }

    function showAllChildFields(form) {
        form.querySelectorAll('.form-control, input[type="file"]').forEach((item) => {
            showFormGroupOfField(item);
        });
    }

    function handleTemplateSelectionChange(event) {
        let id = event.target.value;
        updateVisibleFormFields(id);
    }

    function handleModalShown() {
        console.log('modal shown');
        let templateSelect = document.querySelector('#hypertargeting_template_id');
        if (templateSelect && templateSelect.value.length) {
            console.log(templateSelect.value);
            updateVisibleFormFields(templateSelect.value);
        }
    }

    function updateVisibleFormFields(templateId) {
        getAllowedFields(templateId).then((response) => {
            allowed = response.fields;
            allowed.push('hypertargeting_template_id');
            hideAllChildFields(addHtForm);
            allowed.forEach((field) => {
                switch (field) {
                    case 'blocks':
                        let blocks = addHtForm.querySelector('.blocks');
                        if (blocks) {
                            showAllChildFields(blocks);
                        }
                        break;
                    case 'facts':
                        let facts = addHtForm.querySelector('.facts');
                        if (facts) {
                            showAllChildFields(facts);
                        }
                        break;
                    case 'testimonials':
                        let testimonials = addHtForm.querySelector('.testimonials');
                        if (testimonials) {
                            showAllChildFields(testimonials);
                        }
                        break;
                    case 'about_logos':
                        let aboutLogos = addHtForm.querySelector('.aboutLogos');
                        if (aboutLogos) {
                            showAllChildFields(aboutLogos);
                        }
                        break;
                    default:
                        let item = addHtForm.querySelector('[name=' + field + ']');
                        if (item) {
                            showFormGroupOfField(item);
                        }
                }
            });
        }).catch(() => {
            showAllChildFields(addHtForm);
        });
    }
})();