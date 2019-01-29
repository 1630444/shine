

void function( exports, $, _, Backbone ) {

	exports.JSNES_Import_ExportrView = B.View.extend( {
		el: '#jsnes-import-export',
		events: {
			'click .jsnes-export-data': 'exportData',
			'click .jsnes-import-data': 'importData',
			'click .close, .jsnes-close': 'hide',
			'click .jsnes-save': 'saveData',
		},
		initialize: function(options) {
			this.data = this.data || {};
			this.data.title = 'Export data';
			this.data.status = 'export';
			this.render();
		},
		render: function(){
			if ( this.data && this.data.title) {
				this.$('.modal-title').text(this.data.title);
				this.$('.modal-body').html('');
			}
		},
		checkSelected: function(){
			var view = this;
			var listSlider = $('.jsn-column-select input[type="checkbox"]:checked');
			this.listID = [];
			if(listSlider.length > 0) {
				_.each(listSlider, function(checkbox){
					var id = $(checkbox).parent('.jsn-column-select').siblings('.jsn-column-id').text();
					var title = $(checkbox).parent('.jsn-column-select').siblings('.jsn-column-title').find('a').text();
					view.listID.push({id: id, title: title});
				})
			}
			return this.listID;
		},
		exportData: function(){
			console.log('export');
			this.data.status = 'export';
			var view = this;
			var data = JSNES_SlidersData || {};
			var listID = this.checkSelected();
			if(listID.length > 0) {
				this.$('.modal').removeClass('hidden');
				this.$('.modal-body').html('');
				_.each(listID, function(obj){
					view.renderOneRow(data[obj.id], obj);
				});
			}
			else {
				alert('Please first make a selection from the list');
			}

		},
		importData: function(){
			//console.log('import')
			this.data.status = 'import';
			this.data.title = 'Import data';
			this.render();
			$('.jsnes-import-input').trigger('click');
		},
		renderOneRow: function(dataRow, obj){
			var re = new RegExp("^(http|https)://", "i");
			var html = '<ul class="list-slide" slider-id="' + obj.id + '">';
			var data = JSON.parse(dataRow);
			html += '<li><strong>' + obj.title + '</strong></li>';
			_.each(data.slides, function(slide){
				
				var style = '';
				var sindex = 0;
				style += 'background-color:' + ( typeof slide.background !== 'undefined' && typeof slide.background.color !== 'undefined' ? slide.background.color : '') + ';';
				style += 'background-position: center center;';
				style += 'background-size: cover;';
				if (typeof slide.background !== 'undefined' && typeof slide.background.image != 'undefined')
				{
					if (re.test(slide.background.image.src))
					{	
						style += 'background-image: url(' + slide.background.image.src + ');';
					}
					else
					{
						style += 'background-image: url(' + JSNES_UrlRoot +  slide.background.image.src + ');';
					}	
				}
				if ( typeof slide.background !== 'undefined' && typeof slide.background.color !== 'undefined') {
					style += 'background-color:' + slide.background.color + ';';
				}

				if (typeof slide.index != 'undefined')
				{
					sindex = slide.index;
				}
				html += '<li class="slide-data" slide-index="' + sindex + '" style="' + style + '">';
				html += '<input type="checkbox" />';
				html += '</li>';
			});
			html += '</ul>';
			this.$('.modal-body').height($(window).height()*0.9);
			this.$('.modal-body').append(html);
			this.$('.modal-body input[type="checkbox"]').prop('checked', true);
		},
		show: function(){
			this.$('.modal').removeClass('hidden');

		},
		hide: function(){
			$('.jsnes-import-input').val('');
			this.$('.modal').addClass('hidden');
		},
		saveData: function(){
			if(this.data.status == 'export') {
				this.exportSelectedData();
			}
			else if(this.data.status == 'import') {
				this.getImportDataSelected();
			}
		},
		getImportDataSelected: function(){
			var listUL = this.$('.modal-body ul.list-slide');
			var data = [];
			var view = this;
			if(this.importDataArray.length > 0){
				_.each(listUL, function(ul){
					var sliderID = parseInt($(ul).attr('slider-id'));
					var oneData = view.importDataArray[sliderID];
					var listSlides = $(ul).find('li.slide-data input:checked');
					var listSlidesData = [];
					_.each(listSlides, function(input){
						var $li = $(input).parent();
						var index = parseInt($li.attr('slide-index'));
						var slideData = _.findWhere(oneData.slides, {index: index});

                        if(typeof slideData === 'undefined') {
                            slideData = oneData.slides[index];
                        }
                        listSlidesData.push(slideData);
					});
					oneData.slides = listSlidesData;
					data.push(oneData);
				});
				//console.log(data)
			}
			view.hide();

			if(data.length > 0) {
				var token = $('#jsn-page-list').attr('token');
				$.ajax({
					url: 'index.php?option=com_easyslider&task=sliders.importData&' + token + '=1',
					type: 'POST',
					dataType: 'json',
					data: {import_data: JSON.stringify(data)},
					error: function () {
						//console.log('error');
					},
					complete: function(data){
						var json = JSON.parse(data.responseText);
						window.location.reload();
						if(typeof json !== 'undefined') {
							//alert(json.message);
						}
					}
				});
			}
		},
		exportSelectedData: function(){
			var listUL = this.$('.modal-body ul.list-slide');
			var data = {};
			_.each(listUL, function(ul){
				var title = $(ul).find('li').first().text();
				var sliderID = parseInt($(ul).attr('slider-id'));
				var oneData = JSON.parse(JSNES_SlidersData[sliderID]);
				oneData.title = title;
				var listSlides = $(ul).find('li.slide-data input:checked');
				var listSlidesData = [];
				_.each(listSlides, function(input){
					var $li = $(input).parent();
					var index = parseInt($li.attr('slide-index'));
					var slideData = _.findWhere(oneData.slides, {index: index});
					if(typeof slideData === 'undefined') {
                        slideData = oneData.slides[index];
					}
                    listSlidesData.push(slideData);
				});
				oneData.slides = listSlidesData;
				data[sliderID] = oneData;
			});

			var	json = JSON.stringify(data)
			var blob = new Blob([json], {type: "application/json"});
			var url  = URL.createObjectURL(blob);

			var currentdate = new Date();
			var datetime = currentdate.getDate() + "/"
				+ (currentdate.getMonth()+1)  + "/"
				+ currentdate.getFullYear() + "-"
				+ currentdate.getHours() + ":"
				+ currentdate.getMinutes();

			var a = document.createElement("a");

			a.href        = url;
			a.download    = datetime + ".json";
			a.style = "display: none";
			document.body.appendChild(a);

			a.click();
			this.hide();
		},
		importDataFromFile: function(file){
			var view = this;
			this.importDataArray = [];
			var contents = '';
			if (file) {
				if(this.checkFileAPI) {

					var read = new FileReader();
					read.onload = function(e){
						contents = e.target.result;
					};
					read.onloadend = function(){

						if (/^[\],:{}\s]*$/.test(contents.replace(/\\["\\\/bfnrtu]/g, '@').
								replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
								replace(/(?:^|:|,)(?:\s*\[)+/g, '')))
						{
							//the json is ok
							var data = JSON.parse(contents);
							_.each(data, function(slider, id){
								var obj ={};
								obj.id = id;
								obj.title = slider.title;
								view.importDataArray[id]= slider;
								view.renderOneRow(JSON.stringify(slider), obj);
							});
							view.show();
						}
						else
						{
							alert('this file is not JSON');
						}
					};
					read.readAsText(file);
				}

			}
			else {
				alert('Failed to load this file');
			}

		},
		checkFileAPI:function() {
			if (window.File && window.FileReader && window.FileList && window.Blob) {
				return true;
			} else {
				alert('The File APIs are not fully supported by your browser. Fallback required.');
				return false;
			}
		},
	} );

	var importBTN = $('.jsnes-import').parent();
	var exportBTN = $('.jsnes-export').parent();
	importBTN.removeAttr('onclick');
	exportBTN.removeAttr('onclick');
	importBTN.append('<input type="file" class="jsnes-import-input" style="display:none;">');
	$('.jsnes-import-input').click(function(e){
		e.stopPropagation();
	});

	var dataView = new JSNES_Import_ExportrView({ sliders: [], model: new Backbone.Model });

	$('.jsnes-import-input').on('change',function(e){
		var file = e.currentTarget.files[0];
		dataView.importDataFromFile(file);
		//reset files array
		e.currentTarget.value = '';
	});

	importBTN.click(function(e){
		dataView.importData();
	});
	exportBTN.click(function(e){
		dataView.exportData();
	});
}( this, JSNES_jQuery, JSNES_Underscore, JSNES_Backbone );

