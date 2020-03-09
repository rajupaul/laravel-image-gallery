@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row ">
      
         <div class="col-md-12">
            <div class="card">
                <div class="card-header">
               <div class="d-flex justify-content-between">
                 <div class="">Images</div>
                 <div class="">
                    <div class="form-inline">
                      <label for="category">Sort By &nbsp;</label>
                      <select class="form-control" id="sort_by" onchange="sort(this.value)">
                        <option value="latest" {{((Request::query('sort_by') && Request::query('sort_by')=='latest' ) || !Request::query('sort_by') )?'selected':''}}>Latest</option>
                        <option value="oldest" {{(Request::query('sort_by')=='oldest')?'selected':''}}>Oldest</option>
                      </select>
                    </div>
                 </div>
                </div>
                </div>

                <div class="card-body" style="min-height: 80vh;">
                  <div class="row">
                      <div class="col-md-4">
                        <p>Filter By Category</p>
                        <ul class="list-group">
                          <li class="list-group-item {{(!Request::query('category'))?'active':''}}" onclick="on_category_change('')">All</li>
                          <li class="list-group-item {{(Request::query('category')=='friends')?'active':''}}" onclick="on_category_change('friends')">Friends</li>
                          <li class="list-group-item {{(Request::query('category')=='family')?'active':''}}" onclick="on_category_change('family')">Families</li>
                          <li class="list-group-item {{(Request::query('category')=='others')?'active':''}}" onclick="on_category_change('others')">Others</li>
                        </ul>
                      </div>
                      <div class="col-md-8">
                         @if($errors->any())
                          @foreach($errors->all() as $error)
                          <div>{{$error}}</div>
                          @endforeach
                         @endif
                          <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo">Add Image</button>
                          <div id="demo" class="collapse">
                                        <div class="row">
                              <div class="col-md-12">
                                  <form action="{{route('store-image')}}" method="post" id="image_form" enctype="multipart/form-data">
                                    @csrf
                                      <div class="form-group">
                                        <label for="caption">Image Caption</label>
                                        <input name="caption" type="text" class="form-control" placeholder="Enter caption" id="caption">
                                      </div>
                                    <div class="form-group">
                                      <label for="category">Category</label>
                                      <select class="form-control" name="category" id="category">
                                        <option value="">Select Category</option>
                                        <option value="friends">Friends</option>
                                        <option value="family">Family</option>
                                        <option value="others">Others</option>
                                      </select>
                                    </div>

                                   <div class="form-group">
                                    <label class="control-label">Upload File</label>
                                    <div class="preview-zone hidden">
                                      <div class="box box-solid">
                                        <div class="box-header with-border">
                                          <div><b>Preview</b></div>
                                          <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-danger btn-xs remove-preview">
                                              <i class="fa fa-times"></i> Reset
                                            </button>
                                          </div>
                                        </div>
                                        <div class="box-body"></div>
                                      </div>
                                    </div>
                                    <div class="dropzone-wrapper">
                                      <div class="dropzone-desc">
                                        <i class="glyphicon glyphicon-download-alt"></i>
                                        <p>Choose an image file or drag it here.</p>
                                      </div>
                                      <input type="file" name="image" class="dropzone">
                                    </div>

                                    <div id="image_error"></div>
                                  </div>
                                      <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                              </div>
                          </div>
                          </div>
      
                          <div class="row mt-2">

                            @forelse($images as $image)
                             <div class="col-md-3 mb-4">
                                <a title="{{$image->caption}}" class="fancybox" href="{{asset('user_images/'.$image->image)}}" data-fancybox="{{$image->category}}" data-id="{{$image->id}}" data-caption="{{$image->caption}}">
                                    <img height="100%" width="100%" src="{{asset('user_images/thumbnail/'.$image->image)}}" alt="" />
                                </a>
                              </div>


                            @empty
                                   <div class="col-md-3">
                                <p class="text-danger">No Images</p>
                            </div>
                            @endforelse
                            @if(count($images))
<div class="col-md-12">{{$images->appends(Request::query())->links()}}</div>
                            @endif
                          </div>




                      </div>
                  </div>

                </div>
            </div>
        </div>
    </div>
</div>

<form action="" method="post" id="image-delete-form">
    @csrf
    @method('DELETE');
</form>
@endsection
@section('javascript')
<script type="text/javascript">

    var query={};

    @if(Request::query('category'))
     Object.assign(query,{"category":"{{Request::query('category')}}"});
    @endif
    @if(Request::query('sort_by'))
     Object.assign(query,{"sort_by":"{{Request::query('sort_by')}}"});
    @endif

    function sort(value){
       Object.assign(query,{"sort_by":value});
       window.location.href="{{route('home')}}?"+$.param(query);
    }

    function on_category_change(value){
       Object.assign(query,{"category":value});
       window.location.href="{{route('home')}}?"+$.param(query);
    }

    $( "#image_form" ).validate({
          onchange:true,
          rules: {
            caption: {
              required: true
            },
            category: {
              required: true
            },
           image: {
              required: true,
              extension: "png|jpeg|jpg|bmp"
            }
          },
          messages:{
            caption:{
                required:"Please enter caption text."
            },
            category:{
              required:"Please select a category."
            },
            image:{
                required:"Please upload an image.",
                extension:"Only png,jpeg,bmp image supported."
            }
          },
           errorPlacement: function(error, element) {
            if (element.attr("name") == "image"  ) {
              error.insertAfter("#image_error");
            } else {
              error.insertAfter(element);
            }
          }
        });




 // $(".fancybox").fancybox({
 //  afterLoad : function() {
 //   this.title = $("#fancyboxTitles div").eq(this.index).html();
 //  }
 // });

 $.fancybox.defaults.btnTpl.delete = '<button data-fancybox-delete class="fancybox-button fancybox-button--delete" title="Delete">Delete</button>';
 $.fancybox.defaults.buttons = ['delete','close','download','share'];


var current_image_id='';
 $(".fancybox")
    .attr('rel', 'gallery')
    .fancybox({
    caption : function( instance, item ) {
     return item.opts.caption;
    },
    beforeShow: function (instance, item) {
        current_image_id=this.opts.id;    
    },
    afterShow: function () {

    },
    helpers: {
        title: {
            type: 'inside'
        }, //<-- add a comma to separate the following option
        buttons: {} //<-- add this for buttons
    },
    // closeBtn: false, // you will use the buttons now
    // arrows: false
});

    function readFile(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {

        const validImageTypes = ['image/gif', 'image/jpeg', 'image/png'];
        if (!validImageTypes.includes(input.files[0]['type'])) {
                       var htmlPreview =
        '<p>Image preview not available</p>' +
        '<p>' + input.files[0].name + '</p>'; 
        }else{
            var htmlPreview =
        '<img width="70%" height="200px;" src="' + e.target.result + '" />' +
        '<p>' + input.files[0].name + '</p>';   
        }

   
      

      var wrapperZone = $(input).parent();
      var previewZone = $(input).parent().parent().find('.preview-zone');
      var boxZone = $(input).parent().parent().find('.preview-zone').find('.box').find('.box-body');

      wrapperZone.removeClass('dragover');
      previewZone.removeClass('hidden');
      boxZone.empty();
      boxZone.append(htmlPreview);
    };

    reader.readAsDataURL(input.files[0]);
  }
}

function reset(e) {
  e.wrap('<form>').closest('form').get(0).reset();
  e.unwrap();
}

$(".dropzone").change(function() {
  readFile(this);
});

$('.dropzone-wrapper').on('dragover', function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).addClass('dragover');
});

$('.dropzone-wrapper').on('dragleave', function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).removeClass('dragover');
});

$('.remove-preview').on('click', function() {
  var boxZone = $(this).parents('.preview-zone').find('.box-body');
  var previewZone = $(this).parents('.preview-zone');
  var dropzone = $(this).parents('.form-group').find('.dropzone');
  boxZone.empty();
  previewZone.addClass('hidden');
  reset(dropzone);
});

$('body').on('click', '[data-fancybox-delete]', function(e) {



    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.value){
        
        $('#image-delete-form').attr('action',base_url+'/delete-image/'+current_image_id);
        $('#image-delete-form').submit();
        // Swal.fire(
        //   'Deleted!',
        //   'Your file has been deleted.',
        //   'success'
        // )
      }
    })



});
</script>
@endsection