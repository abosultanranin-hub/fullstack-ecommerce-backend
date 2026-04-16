<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الفئات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .action-btns {
            white-space: nowrap;
        }
        .edit-form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <h1 class="text-center mb-4">إدارة الفئات</h1>
        
        <!-- نموذج إضافة فئة جديدة -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3>إضافة فئة جديدة</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('category.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الفئة</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">الفئة الأب</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                            <option value="">لا يوجد</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ الفئة</button>
                </form>
            </div>
        </div>
        
        <!-- نموذج تعديل الفئة (مخفي بشكل افتراضي) -->
        <div class="card mb-4 edit-form" id="editCategoryFormContainer">
            <div class="card-header bg-warning text-dark">
                <h3>تعديل الفئة</h3>
            </div>
            <div class="card-body">
                <form id="editCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editCategoryId" name="id">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">اسم الفئة</label>
                        <input type="text" class="form-control" id="editCategoryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryDescription" class="form-label">الوصف</label>
                        <textarea class="form-control" id="editCategoryDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryParent" class="form-label">الفئة الأب</label>
                        <select class="form-control" id="editCategoryParent" name="parent_id">
                            <option value="">لا يوجد</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning">تحديث الفئة</button>
                    <button type="button" class="btn btn-secondary" id="cancelEdit">إلغاء</button>
                </form>
            </div>
        </div>
        
        <!-- جدول عرض الفئات -->
        <div class="card">
            <div class="card-header bg-light">
                <h3>قائمة الفئات</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الفئة</th>
                                <th>الوصف</th>
                                <th>Slug</th>
                                <th>الفئة الأب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description ?? '---' }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->parent->name ?? '---' }}</td>
                                <td class="action-btns">
                                    <button class="btn btn-sm btn-warning edit-btn" 
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-description="{{ $category->description ?? '' }}"
                                        data-parent="{{ $category->parent_id ?? '' }}">
                                        تعديل
                                    </button>
                                    <form action="{{ route('category.destroy', $category->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // عند النقر على زر التعديل
        $(document).on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const description = $(this).data('description');
            const parent = $(this).data('parent');
            
            $('#editCategoryId').val(id);
            $('#editCategoryName').val(name);
            $('#editCategoryDescription').val(description);
            $('#editCategoryParent').val(parent);
            
            // تحديث رابط النموذج
            $('#editCategoryForm').attr('action', '/category/' + id);
            
            $('#editCategoryFormContainer').show();
            $('html, body').animate({
                scrollTop: $('#editCategoryFormContainer').offset().top
            }, 500);
        });
        
        // إلغاء التعديل
        $('#cancelEdit').click(function() {
            $('#editCategoryFormContainer').hide();
        });
    });
    </script>
</body>
</html>