$(document).ready(function() {
	AddToHistory("get","recent")
	LoadSystem("recent");

	/*
	* Event Listeners
	*/
	$('.navbar_system').on("click", function(e){
		AddToHistory("system",$(this).attr('systemid'))
		LoadSystem($(this).attr('systemid'));
	});
	
	$('#btn_add_change').click(function(){
		AddToHistory("add","change");
		AddChange();
	});

	$('#frm_search').submit(function(event){
		AddToHistory("search",$('#frm_search input').val());

		GenerateTableOffSearch($('#frm_search input').val());
		event.preventDefault();
	});

	$('#btn_calendar_toggle').click(function(){
		AddToHistory("get","calendar");
		OpenCalendar();

	}); 

	$('.dropdown-menu').on("click", "li a", function(){
      var selText = $(this).text();
      $(this).parents('.dropdown').find('.dropdown-toggle').html(selText+' <span class="caret"></span>').attr('data_id',$(this).attr('id'));
    });
	

	$('.navbar-brand').on("click", function(e){
		AddToHistory("get","recent");
		var sub_url = window.location.pathname.match("^\/([^\/]*)\/")[1];
		window.location = "/"+sub_url;
	});

	$('#btn_logout').on("click", function(e){
		var sub_url = window.location.pathname.match("^\/([^\/]*)\/")[1];
		window.location = "/"+sub_url+"/logout";
	});

	$("#add_modal_save").click(function(){
		SaveNewChange()
	});

    $("#add_modal_close").click(function(){
    	CloseAddModal();
    });

    $(document).on('click','.change_summary', function(){
    	AddToHistory("get",$(this).attr('data_id'));
    	ViewChange($(this).attr('data_id'));
    });

    $('#start_value').datetimepicker({
      weekStart: 1,
      todayBtn:  1,
      autoclose: 1,
      todayHighlight: 1,
      startView: 2,
      forceParse: 0,
      showMeridian: 1,
      startDate: "2015-04-01",
    });

    $(window).bind("popstate", function(event){
		console.log(event);
  		if(event.originalEvent.state) {
  			CloseAddModal();
  			$('#view_modal').modal('hide');
  			switch(event.originalEvent.state.module){
  				case "get":
  					if (event.originalEvent.state.value == 'recent'){
  						LoadSystem("recent");
  					}
  					else if(event.originalEvent.state.value == 'calendar'){
  						OpenCalendar();
  					}
  					else if($.isNumeric(event.originalEvent.state.value)){
  						ViewChange(event.originalEvent.state.value);
  					}
  					break;
  				case "add":
  					AddChange();
  					break;
  				case "system":
  					LoadSystem(event.originalEvent.state.value);
  					break;
  				/* NOT IMPLEMENTING EVENT MONITORING ON CALENDAR DATES BECAUSE IT GETS TO MESSY
  				case "calendar":
  					if (event.originalEvent.state.value == 'year' || 
  						event.originalEvent.state.value == 'month' || 
  						event.originalEvent.state.value == 'week' || 
  						event.originalEvent.state.value == 'day' ){
  						calendar.view(event.originalEvent.state.value);
  					}
  					else {
  						calendar.navigate(event.originalEvent.state.value);
  					}
		  			break;
		  		*/
  				case "search":
  					GenerateTableOffSearch(event.originalEvent.state.value);
  					break;
  				case "datatable":
  					if (event.originalEvent.state.value == "prev"){
  						alert("prev");
  					}
  					else if (event.originalEvent.state.value == "next"){
  						alert("next");
  					}
  					else {
  						alert("search");
  					}
		  			break;
  			}
  		}
  		else{
  			window.history.back();
  		}
	});

    /***
    * Automatic Page Refresh on Inactivity
    */
    idleTime = 0;
    var idleInterval = setInterval(timerIncrement, 60000); // 1 minute
    $(this).mousemove(function (e) {
        idleTime = 0;
    });
    $(this).keypress(function (e) {
        idleTime = 0;
    });

    


    /*#####################################
    * Functions
    #####################################*/

    function OpenCalendar(){
	ClearViewDiv();
	$("#content_area").append("<div class='page-header'><div class='pull-right form-inline'><div class='btn-toolbar'><div class='btn-group'><button class='btn btn-primary' data-calendar-nav='prev'>Prev</button>\
		<button class='btn' data-calendar-nav='today'>Today</button><button class='btn btn-primary' data-calendar-nav='next'>Next</button>\
		</div><div class='btn-group'><button class='btn btn-warning' data-calendar-view='year'>Year</button>\
		<button class='btn btn-warning active' data-calendar-view='month'>Month</button><button class='btn btn-warning' data-calendar-view='week'>Week</button>\
		<button class='btn btn-warning' data-calendar-view='day'>Day</button></div></div></div><h3>Calendar View</h3>\
		</div><div class='row'><div class='span8'><div id='calendar_div'></div></div><div class='span3'><div class='panel panel-default'>\
		<table class='table table-bordered calendar_key'><tr><td class='key-prd'>PRD</td><td class='key-nonprd'>NON PRD</td><td class='key-sandbox'>SandBox</td></tr></table></div><h4 id='changes_label'></h4><ul id='eventlist' class='nav nav-list'></ul></div>");
	window.calendar = $("#calendar_div").calendar({
        tmpl_path: "static/templates/",
        tmpl_cache: false,
        events_source: 'api/calendar',
        view: 'month',
        day: 'now',
        modal_type : "ajax",
		onAfterEventsLoad: function(events) {
			if(!events) {
				return;
			}

			var list = $('#eventlist');
			list.html('');

			//Builds list of changes
			$.each(events, function(key, val) {
				$(document.createElement('li'))
					.html('<a href="'+val.url+'"class="sidebar-'+val.class+' event-item">'+val.title+'</a>')
					.appendTo(list);
			});

		}, //onAfterEventsLoad
		onAfterViewLoad: function(view) {
			$('.page-header h3').text(this.getTitle());
			$('#changes_label').text("All Changes for "+this.getTitle());
			$('.btn-group button').removeClass('active');
			$('button[data-calendar-view="' + view + '"]').addClass('active');
		},
		classes: {months: {general: 'label'}}

    });

    $('.btn-group button[data-calendar-nav]').each(function() {
		$(this).click(function() {
			if ($(this).data('calendar-nav') == 'prev'){
				//AddToHistory("calendar","next")
			}
			else if ($(this).data('calendar-nav') == 'next'){
				//AddToHistory("calendar","prev");
			}
			else{
				//AddToHistory("calendar",$(this).data('calendar-nav'));
			}
			calendar.navigate($(this).data('calendar-nav'));
		});
	});

	$('.btn-group button[data-calendar-view]').each(function() {
		$(this).click(function() {
			//AddToHistory("calendar",$(this).data('calendar-view'));
			calendar.view($(this).data('calendar-view'));
		});
	});
}

});
    function timerIncrement() {
      idleTime = idleTime + 1;
      if (idleTime > 4) { // 5 minutes
          window.location.reload();
      }
    }  

    function SaveNewChange(){
	    var start = $("#start_value").val();
	    var duration = $(".duration_value").attr('data_id');
	    var summary = $(".summary_value").val();
	    var change = $(".note-editable").code();
	    var owner = $(".owner_value").attr('data_id');
	    var environment = $(".environment_value").attr('data_id');
	    var system = $(".system_value").attr('data_id');
	    var json_object;

	    console.log("start"+start);
	    console.log("duration"+duration);
	    console.log("summary"+summary);
	    console.log("change"+change);
	    console.log("owner"+owner);
	    console.log("environment"+environment);
	    console.log("system"+system);

	    if (start !== "Click Me" && duration !== "undefined" && summary !== "" && change !== "Add Change Here!" && owner !== "undefined" && environment !== "undefined" && system !== "undefined") {
			$.post( "api/change",{ 
				start: start,
				duration: duration,
				summary: summary, 
				change: change, 
				owner: owner, 
				environment: environment, 
				system: system
			}, function( data ) {
				json_object = JSON.parse(data);
				console.log(json_object);
				if (json_object.success === 1) {
					alert("Change Sucessfully Added to Database.");
					CloseAddModal();
            		location.reload(true);
				} else {
					alert("An Error Has Occured. Please Contact Your System Administrator.");
				} 
			});
		}
		else {
			alert("Please fill out ALL fields");
		}
	}

	function AddChange(){

		$('#summernote').summernote({
			height: 224,
			toolbar: [
				['style', ['style','bold','italic', 'underline','fontsize']],
				['insert', ['picture','link','hr']],
				['layout', ['ul','ol']],
				['misc', ['undo','redo','fullscreen']]
			],
			onfocus: function(e) {
				if ($('.note-editable').text() == "Add Change Here!"){
					$('.note-editable').text("");
				}
  			}
		});

		//Fetching durations
		$.getJSON("api/durations", function(data){
			var html = "";
			$.each(data.data, function(key,val){
				html += "<li role='presentation'><a role='menuitem' tabindex='-1' href='javascript:void(0);' id='"+val.id+"'>"+val.duration+"</a></li>";
			})
			$('#add_duration').html(html);
		});

		//Fetching owners
		$.getJSON("api/owners", function(data){
			var html = "";
			$.each(data.data, function(key,val){
				html += "<li role='presentation'><a role='menuitem' tabindex='-1' href='javascript:void(0);' id='"+val.id+"'>"+val.full_name+"</a></li>";
			})
			$('#add_owner').html(html);
		});

		//Fetching environment
		$.getJSON("api/environments", function(data){
			var html = "";
			$.each(data.data, function(key,val){
				html += "<li role='presentation'><a role='menuitem' tabindex='-1' href='javascript:void(0);' id='"+val.id+"'>"+val.env_name_short+"</a></li>";
			})
			$('#add_environment').html(html);
		});

		//Fetching system
		$.getJSON("api/systems", function(data){
			var html = "";
			$.each(data.data, function(key,val){
				html += "<li role='presentation'><a role='menuitem' tabindex='-1' href='javascript:void(0);' id='"+val.id+"'>"+val.system_name+"</a></li>";
			})
			$('#add_system').html(html);
		});


		$('#add_change').modal({
			'backdrop': 'static'
		});
	}

	function ShowCalendar(){
		$('#normal_modal').modal('show');
	}

	function BuildTable(alert_text){
		var table_html = "<div class='alert alert-info hide' role='alert' id='table_alert'>"+alert_text+"</div><div class='table-responsive'><table class='table table-striped table-hover' id='ChangeTable'><thead><tr><th>Date/Time</th><th class='col-md-6'>Summary of Change</th><th>Changer</th><th>Environment</th><th>System</th></tr></thead></table></div>";
		$('#content_area').append(table_html);
	}

	function ClearViewDiv(){
		$('#dd_system').html("Systems <span class='caret'></span>");
		$('#ChangeTable').DataTable().clear();
		$('#ChangeTable').DataTable().destroy();
		$("#content_area").html("");
	}

	function GenerateTable(criteria){
		if ($('#ChangeTable').length <= 0){
			
			if(criteria == "recent"){
				BuildTable("Welcome to the <strong>Change Log Repository</strong>... Below is a preview of <strong>ALL</strong> the changes over the last <strong>30 days</strong>.");
				$('#table_alert').removeClass('hide');
			}
			else{
				var system_name = criteria
				BuildTable("Below are the global results for the system: <strong>"+system_name+"</strong>.");
				$('#table_alert').removeClass('hide');
			}
			
		}

		

		$('#ChangeTable').dataTable( {
			"autoWidth": false,
			"ajax": "api/"+criteria,
			"columns": [
				{ "data": "start_time","className": "strong" },
				{ "data": "summary", "render": function( data,type,full,meta){return "<a href='javascript:void(0);' class='strong change_summary' data_id='"+full.id+"'>"+data+"</a>";} },
				{ "data": "full_name","className": "strong" },
				{ "data": "env_name_short","className": "strong" },
				{ "data": "system_name","className": "strong" }
			],
			"order": [[ 0, "desc" ]]
		});
	}

	function GenerateTableOffSearch(search_criteria){
		ClearViewDiv();

		if ($('#ChangeTable').length <= 0){
			BuildTable("Below are the global results for the search: <strong>"+search_criteria+"</strong>.");
			$('#table_alert').removeClass('hide');
		}

		$('#ChangeTable').dataTable( {
			"autoWidth": false,
			"ajax": "api/search/"+search_criteria,
			"columns": [
				{ "data": "start_time","className": "strong" },
				{ "data": "summary", "render": function( data,type,full,meta){return "<a href='javascript:void(0);' class='strong change_summary' data_id='"+full.id+"'>"+data+"</a>";} },
				{ "data": "full_name","className": "strong" },
				{ "data": "env_name_short","className": "strong" },
				{ "data": "system_name","className": "strong" }
			],
			"order": [[ 0, "desc" ]]
		});
		$('.dropdown').removeClass('open');
	}

	function LoadSystem(system){
		//Getting name of label
		if (system != "recent"){
			$('#dd_system').html("<span class='glyphicon glyphicon-hdd'></span> "+system+" <span class='caret'></span>");
		}
		else{
			var lbl_text = "<span class='glyphicon glyphicon-hdd'></span> Systems <span class='caret'></span>";
		}

		ClearViewDiv();

		GenerateTable(system);
		$('#dd_system').html(lbl_text);
	}

	function CloseAddModal(){
		$('#add_change').modal('hide');
		$("#start_value").val("");
		$('.duration_value').html("Select Duration <span class='caret'></span>");
		$(".summary_value").val("");
		$(".change_value").val("");
		$(".note-editable").text("Add Change Here!");
		$(".owner_value").html("Select Owner <span class='caret'></span>");
		$(".environment_value").html("Select Environment <span class='caret'></span>");
		$(".system_value").html("Select System <span class='caret'></span>");
		$("#verify_tip").hide();
	}	


function ViewChange(change_id){
	$.getJSON("api/change/"+change_id, function(data){
		$('#view_modal_datails').html("<tbody>\
				<tr><td><strong>Start Time: </strong></td>\
	            	<td>"+data.data[0].start_time+"</td>\
	          	</tr>\
	          	<tr><td><strong>Duration: </strong></td>\
	            <td>"+data.data[0].duration+"</td>\
	            </tr>\
	          	<tr><td><strong>Summary: </strong></td>\
	          	<td>"+data.data[0].summary+"</td>\
	          	</tr>\
	          	<tr><td><strong>Owner: </strong></td>\
	            <td>"+data.data[0].full_name+"</td>\
	            </tr>\
	          	<tr><td><strong>Environment: </strong></td>\
	          	<td>"+data.data[0].env_name_short+"</td>\
	          	</tr>\
	          	<tr><td><strong>System: </strong></td>\
	            <td>"+data.data[0].system_name+"</td>\
	          </tr></tbody>");
		$('#view_modal_change').html(data.data[0].change);
		$('#view_modal_title').html(data.data[0].summary);
	});
	$("#view_modal").modal();
}

function AddToHistory(module,value){
	history.pushState(
	{
		module: module,
		value: value,
	}, null, "");
}	


