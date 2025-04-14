<div class="col-md-4 service-box">
    <div class="card mb-4 box-shadow">
        <a href="{{ route('category.show', $category->id) }}">
            <p class="card-text service-box-title text-center"><b>{{ $category->title }}</b></p>
            <div class="col-md-12 text-center">
                <div class="d-flex justify-content-center align-items-center" style="min-height: 230px;">
                    <img class="card-img-top img-fluid" src="{{ url('img/service-category-images/' . $category->image) }}?w=298&h=250"
                        alt="{{ $category->title }}">
                </div>
            </div>
        </a>
    </div>
</div>
