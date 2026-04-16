<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إنشاء دور جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>إنشاء دور جديد</h2>

    {{-- عرض رسالة النجاح --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- عرض أخطاء التحقق --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">اسم الدور:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <h4 class="mt-4">اختر الصلاحيات:</h4>
        <div class="mb-2">
            <button type="button" class="btn btn-sm btn-secondary" onclick="togglePermissions(true)">تحديد الكل</button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="togglePermissions(false)">إلغاء التحديد</button>
        </div>

        <div class="row">
            @foreach(config('permissions') as $label => $value)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $value }}" id="perm_{{ $loop->index }}">
                        <label class="form-check-label" for="perm_{{ $loop->index }}">
                            {{ $label }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary mt-3">حفظ</button>
    </form>
</div>

<script>
    function togglePermissions(selectAll) {
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = selectAll);
    }
</script>

</body>
</html>
