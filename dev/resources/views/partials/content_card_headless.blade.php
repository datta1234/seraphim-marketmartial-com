<div class="card mt-5">
  <div class="card-body">
    <h2 class="card-title text-center">
		{{ $title or null}}
    </h2>
    {{ $decorator or null}}
    <div class="card-text">
		{{ $body }}
    </div>
  </div>
</div>