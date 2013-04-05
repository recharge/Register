    (function(){ 
       var a={
           exec:function(editor){
          	sayhey();
          }
       }
          
         b='upload'; 
         CKEDITOR.plugins.add(b,{ 
             init:function(c){ 
                 c.addCommand(b,a); 
                 c.ui.addButton('upload',{ 
                     label:'Upload File',
                     command:b 
                 }); 
             } 
         });     
    })();
       
