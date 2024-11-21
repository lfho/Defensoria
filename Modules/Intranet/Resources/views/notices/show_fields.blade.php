<div class="row">
   <!-- Title Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">@lang('Title'):</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.title }}.</dd>
</div>


<div class="row">
   <!-- Content Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">Contenido:</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.content }}.</dd>
</div>


<div class="row">
   <!-- Users Name Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">Creador:</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.users_name }}.</dd>
</div>


<div class="row">
   <!-- State Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">@lang('State'):</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.state }}.</dd>
</div>


<div class="row">
   <!-- Date Start Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">@lang('Start Date'):</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.date_start }}.</dd>
</div>


<div class="row">
   <!-- Date End Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">@lang('End Date'):</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.date_end }}.</dd>
</div>


<div class="row">
   <!-- Views Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">@lang('Views'):</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.views }}.</dd>
</div>


<div class="row">
   <!-- Image Presentation Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">Imagen presentación:</dt>
   <img width="200" v-if="dataShow.image_presentation?.indexOf('storage') == -1" :src="'{{ asset('storage') }}/'+dataShow.image_presentation" alt="">
   <img width="200" v-else :src="dataShow.image_presentation" alt="">

</div>

<div class="row">
   <!-- Image Banner Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">Imagen Banner:</dt>
   <img width="200" v-if="dataShow.image_banner?.indexOf('storage') == -1" :src="'{{ asset('storage') }}/'+dataShow.image_banner" alt="">
   <img width="200" v-else :src="dataShow.image_banner" alt="">

</div>


<div class="row">
   <!-- Keywords Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">Palabras claves:</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.keywords }}.</dd>
</div>


<div class="row">
   <!-- Featured Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">Destacado:</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.featured }}.</dd>
</div>

<div class="row">
   <!-- Intranet News Category Id Field -->
   <dt class="text-inverse text-justify col-sm-3 col-md-3 col-lg-3 ">@lang('Category'):</dt>
   <dd class="col-sm-9 col-md-9 col-lg-9 ">@{{ dataShow.category?.name }}.</dd>
</div>


