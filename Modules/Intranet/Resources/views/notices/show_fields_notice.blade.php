   <div class="card-content">
      <div v-if="dataShow.image_banner?.indexOf('storage') == -1" class="card-img">
            <img width="200" :src="'{{ asset('storage') }}/'+dataShow.image_banner" alt="">
      </div>
      <div v-else class="card-img">
         <img width="200" :src="dataShow.image_banner" alt="">
      </div>
      <div class="card-desc">
         <h4 class="category">@{{dataShow.category?.name}}</h4>

          <h3>@{{dataShow.title}}</h3>
          <p>@{{dataShow.date_start}}</p>

          <p style="white-space: break-spaces;" v-html='dataShow.content'></p>
          <small><i class="fa fa-eye">@{{ dataShow.views }}</i></small>
        
      </div>
  </div>

