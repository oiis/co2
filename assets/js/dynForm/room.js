dynForm = {
    jsonSchema : {
	    title : tradDynForm.addroom,
	    icon : "connectdevelop",
	    type : "object",
        save : function (){
            mylog.log("type : ", $("#ajaxFormModal #type").val());
            var params = { 
               email : userConnected.email , 
               id : uploadObj.id,
               name : $("#ajaxFormModal #name").val() , 
               tags : $("#ajaxFormModal #tags").val().split(","),
               type : $("#ajaxFormModal #type").val(),
               parentId : (contextData.parentId) ? contextData.parentId : contextData.id,
               parentType : (contextData.parentType) ? contextData.parentType : contextData.type
            };

           mylog.dir(params);
            $.ajax({
              type: "POST",
              url: baseUrl+"/"+moduleId+'/rooms/saveroom',
              data: params,
              success: function(data){
                if(data.result){
                  delete window.myActionsList;
                  delete window.myVotesList;
                  toastr.success( "SUCCESS saving Room !");
                  mylog.dir(data);
                  uploadObj.gotoUrl = (uploadObj.gotoUrl) ? "#page.type."+params.parentType+".id."+params.parentId+".view.dda.dir."+$("#ajaxFormModal #type").val() +".idda."+ uploadObj.id : location.hash;
                  dyFObj.elementObj.dynForm.jsonSchema.afterSave();
                }
                else {
                  toastr.error(data.msg);
                }
                $.unblockUI();
              },
              dataType: "json"
            });
        },
	    onLoads : {
	    	sub : function(){
    			$("#ajax-modal .modal-header").removeClass("bg-dark bg-purple bg-red bg-azure bg-green bg-green-poi bg-orange bg-yellow bg-blue bg-turq bg-url")
						  					  .addClass("bg-url");
                uploadObj.gotoUrl = "tmp";
    		},
        onload : function(data){
            if(data && data.type){
                $(".breadcrumbcustom").html( "<h4><a href='javascript:;'' class='btn btn-xs btn-danger'  onclick='dyFObj.elementObj.dynForm.jsonSchema.actions.clear()'><i class='fa fa-times'></i></a> "+tradCategory[data.type]+"</h4>");
                $(".sectionBtntagList").hide();
            } else
                $(".nametext, .imageuploader, .tagstags, #btn-submit-form").hide();
        }
	    },
        beforeBuild : function(){
            dyFObj.setMongoId('actionRooms',function(){});
        },
	    afterSave : function(){
            if( $('.fine-uploader-manual-trigger').fineUploader('getUploads').length > 0 )
                $('.fine-uploader-manual-trigger').fineUploader('uploadStoredFiles');
            else 
            { 
                dyFObj.closeForm(); 
                urlCtrl.loadByHash( uploadObj.gotoUrl );
            }
	    },
        canSubmitIf : function () { 
             return ( $("#ajaxFormModal #type").val() ) ? true : false ;
        },
        actions : {
            clear : function() {
                
                $("#ajaxFormModal #section, #ajaxFormModal #type").val("");

                $(".breadcrumbcustom").html( "");
                $(".sectionBtntagList").show(); 
                $(".nametext, .imageuploader, .tagstags, #btn-submit-form").hide();
            }
        },
	    properties : {
	    	info : {
                inputType : "custom",
                html:"<p><i class='fa fa-info-circle'></i> "+tradDynForm.infocreateRoom+".</p>",
            },
            //parentId : dyFInputs.inputHidden(),
            //parentType : dyFInputs.inputHidden(),
            breadcrumb : {
                inputType : "custom",
                html:"",
            },
            sectionBtn :{
                label : tradDynForm.whichkindofroom+" ? ",
                inputType : "tagList",
                placeholder : "Choisir un type",
                list : roomList.sections,
                trad : tradCategory,
                init : function(){ //console.log("LIST ROOM TYPE", roomList);
                    $(".sectionBtn").off().on("click",function()
                    {
                        $(".typeBtntagList").show();
                        $(".sectionBtn").removeClass("active btn-dark-blue text-white");
                        $( "."+$(this).data('key')+"Btn" ).toggleClass("active btn-dark-blue text-white");
                        $("#ajaxFormModal #type").val( $(this).data('key') );
                        
                        $(".breadcrumbcustom").html( "<h4><a href='javascript:;'' class='btn btn-xs btn-danger'  onclick='dyFObj.elementObj.dynForm.jsonSchema.actions.clear()'><i class='fa fa-times'></i></a> "+$(this).data('tag')+"</h4>");
                        $(".sectionBtntagList").hide();
                        $(".nametext, .imageuploader, .tagstags").show();
                        dyFObj.canSubmitIf();
                    });
                }
            },
            type : dyFInputs.inputHidden(),
            name : dyFInputs.name("room"),
            image : dyFInputs.image(),
            tags : dyFInputs.tags()
	    }
	}
};