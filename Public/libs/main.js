/**
 * 时间戳的格式化
 * @param format
 * @returns {*}
 */
define(function (require, exports, module) {
	var jQuery = require('jquery');
	
	var main = (function ($, window, document, undefined) {
			
			var tsb = {


				/*弹框提示信息*/
				modalAlert: function (options,status,speed) {
					options = typeof options == 'string' ? {msg:options,status:status,speed:speed} : options;
					var opt = $.extend({
						status: "success",
						msg: "成功",
						speed: 2000
					}, options || {});

					var status = {
						error:'danger'
					};

					var cls = $.isEmptyObject(status[opt.status]) ? opt.status : status[opt.status];
                    var icon = cls+'_icon.png';
					var alertHtml = '<div class="modal-alert"><div class="alert alert-' + cls + '"><div class="alert_left"><img src="/Public/static/images/'+icon+'"/></div><div class="alert_right">'  + opt.msg + '</div></div></div>';
                    $(alertHtml).appendTo($('body')).fadeIn().delay(opt.speed).fadeOut(function () {
						$(this).remove()
					});
				},

				/**
				 * congfirm确认提示框
				 * @param data   删除条目的配置信息
				 * @param dom    删除的dom元素
				 * @returns {*}
				 */
               confirm : function(msg,ok,quxiao){
                    var opt = {
                        status: 'success',
                        msg: msg,
                        speed: 2000
                    };

                    var alertIcon = '<img src="/Public/static/images/danger_icon.png"/>';
                    var alertHtml = $('<div class="modal-alert" style="display:none;height:100%;margin-top: -30px;padding-top: 100px"><div class="alert alert-' + opt.status + '">' + alertIcon + opt.msg + '</<div></br></br><input type="button" id="confirm" class="wine_btn wine_btn-blue" style="margin-left: 80px;" value="确定"/>     <input type="button" id="cancle" class="wine_btn wine_btn-blue"  style="margin-left: 40px" value="取消"/></div></div>');

                    $(alertHtml).find("#confirm").click(function(e){
                        $(this).parent().parent().hide();
                        $.isFunction(ok) && ok(e);
                    })

                    $(alertHtml).find("#cancle").click(function(e){
                        $(this).parent().parent().hide();
                        $.isFunction(quxiao) && quxiao(e);
                    })

                    $(alertHtml).appendTo($('body')).fadeIn().delay(opt.speed);
                },


				/*初始化表单元素*/
				initForm: function (oForm, oValue) {
					function initSwitch(oForm) {
						oForm.find('.switch-checkbox').bootstrapSwitch();
					}

					if (oValue == undefined) {
						initSwitch(oForm);
						return false;
					}

					oForm.find('input').each(function (k, oThis) {
						var lableName = $(oThis).attr('name');

						if (lableName) {
							switch ($(oThis).attr('type')) {
								case 'hidden':
								case 'text':
								case 'password':
								case 'select':
									if (oValue.hasOwnProperty(lableName)) {
										$(oThis).val(oValue[lableName]);
									}
									break;
								case 'radio':
									if (oValue.hasOwnProperty(lableName)) {
										oForm.find('input[name="' + lableName + '"][value="' + oValue[lableName] + '"]').attr('checked', 'true');
									}
									break;
								case 'checkbox':
									var _lableName = lableName.substring(0, lableName.length - 2);
									if (oValue.hasOwnProperty(_lableName) && oValue[_lableName]) {
										$.each(oValue[_lableName], function (_k, _v) {
											oForm.find('input[name="' + lableName + '"][value="' + _v + '"]').attr('checked', 'true');
										});
									}

									if (oValue.hasOwnProperty(lableName) && oValue[lableName]) {

										if (lableName == 'status' || lableName.indexOf('_check') > 0) {
											oForm.find('input[name="' + lableName + '"]').val(oValue[lableName]);
										}

										if (oValue[lableName] == app_enum.alert_config_status_normal) {
											oForm.find('input[name="' + lableName + '"]').prop('checked', true);
										} else {
											oForm.find('input[name="' + lableName + '"]').prop('checked', false);
										}

									}
									break;
							}
							if (lableName.substring(lableName.length - 2, lableName.length) != '[]') {
								var js_label = oForm.find('.js_data_' + lableName);
								if (js_label) {
									js_label.text(oValue[lableName]);
								}
							}

						}

					});

					oForm.find('textarea').each(function (k, oThis) {
						var lableName = $(oThis).attr('name');
						if (lableName && oValue.hasOwnProperty(lableName)) {
							$(oThis).html(oValue[lableName]);
						}
					});

					oForm.find('select').each(function (k, oThis) {
						var lableName = $(oThis).attr('name');
						if (oValue.hasOwnProperty(lableName)) {
							$(oThis).val(oValue[lableName]);
						}
					});

					initSwitch(oForm);
				},

				/*url锚点值处理*/
				anchorManager: {
					_processHash: function (params) {
						var url = window.location;
						var hash = '#';
						$.each(params, function (k, v) {
							hash = hash + k + '=' + v + "&";
						});
						url.hash = hash;
					},
					getParams: function () {
						var url = window.location.hash;
						var params = {};
						if (url) {
							url = url.slice(1);
							var _params_tmp = url.split('&');

							if (_params_tmp.length > 0) {
								$(_params_tmp).each(function (k, v) {
									var _tmp = v.split('=');
									if (_tmp[1]) params[_tmp[0]] = _tmp[1];
								})
							}
						}
						return params;
					},
					setParam: function (key, value) {
						var params = this.getParams();
						params[key] = value;
						this._processHash(params);
					},
					removeParam: function (key) {
						var params = this.getParams();
						delete params[key];
						this._processHash(params);
					}
				},


				/**
				 * 弹窗 + 前向翻页
				 */
				modalWithPreNext: function (target) {

					var madal = $('#' + target);
					madal.on("show.bs.modal",function () {

						var loa = $('<div class="location2"><span class="fa fa-chevron-left fa-3"></span></div>' + '<div class="location1"><span class="fa fa-chevron-right fa-3"></span></div>');
						$('body').append(loa);

					}).on("hide.bs.modal", function () {

							$('body').find(".location1,.location2").remove();
					});
				},


                /**
                 * 监听模态弹框，让弹框可以使用多次请求
                 */
                modalEvent : function(){
                    $("#alterModal").on("hidden.bs.modal", function() {
                        $(this).removeData("bs.modal");
                    });
                }


			};
			return tsb;
		})(jQuery, window, document, undefined);


	
	module.exports = main;
});