<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Complaint - GoBEST™ Listens Complaint System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
            padding-top: 2rem;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem;
            border-radius: 1rem 1rem 0 0 !important;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .upload-box {
            border: 2px dashed #e5e7eb;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .upload-box:hover {
            border-color: #2563eb;
            background-color: #f8fafc;
        }

        .upload-icon {
            font-size: 2rem;
            color: #2563eb;
            margin-bottom: 1rem;
        }

        .btn-primary {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            background-color: #2563eb;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-back {
            color: #4b5563;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .btn-back:hover {
            color: #2563eb;
        }

        #preview-container {
            margin-top: 1rem;
        }

        .preview-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .preview-item i {
            color: #2563eb;
            font-size: 1.25rem;
        }

        .preview-item .file-name {
            flex: 1;
            font-weight: 500;
        }

        .preview-item .remove-file {
            color: #ef4444;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <a href="{{ url()->previous() }}" class="btn-back mb-4">
                    <i class="bi bi-arrow-left"></i>
                    Back
                </a>

                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0">New Complaint</h1>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data"
                            id="complaintForm">
                            @csrf
                            <div class="mb-4">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                    id="subject" name="subject" value="{{ old('subject') }}"
                                    placeholder="Enter subject" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="5" placeholder="Enter description" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Attachments</label>
                                <div class="upload-box" id="dropZone">
                                    <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                    <p class="mb-1">Drag and drop files here or click to browse</p>
                                    <p class="text-muted small mb-0">Maximum file size: 50MB per file</p>
                                    <input type="file" id="fileInput" name="attachments[]" class="d-none"
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mov,.avi,.wmv" multiple>
                                </div>
                                <div id="preview-container"></div>
                                @error('attachment')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit Complaint</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
                    const dropZone = document.getElementById('dropZone');
                    const fileInput = document.getElementById('fileInput');
                    const previewContainer = document.getElementById('preview-container');
                    let currentFiles = new DataTransfer();

                    // Handle click on drop zone
                    dropZone.addEventListener('click', () => fileInput.click());

                    // Handle drag and drop
                    dropZone.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        dropZone.style.borderColor = '#2563eb';
                        dropZone.style.backgroundColor = '#f8fafc';
                    });

                    dropZone.addEventListener('dragleave', () => {
                        dropZone.style.borderColor = '#e5e7eb';
                        dropZone.style.backgroundColor = 'white';
                    });

                    dropZone.addEventListener('drop', (e) => {
                        e.preventDefault();
                        dropZone.style.borderColor = '#e5e7eb';
                        dropZone.style.backgroundColor = 'white';

                        if (e.dataTransfer.files.length) {
                            handleFiles(e.dataTransfer.files);
                        }
                    });

                    // Handle file input change
                    fileInput.addEventListener('change', (e) => {
                        if (e.target.files.length) {
                            handleFiles(e.target.files);
                        }
                    });

                    function handleFiles(files) {
                        Array.from(files).forEach(file => {
                            // Check file size (50MB limit)
                            if (file.size > 50 * 1024 * 1024) {
                                alert(`File ${file.name} is larger than 50MB`);
                                return;
                            }

                            // Check if file already exists
                            const existingFile = Array.from(currentFiles.files).find(f =>
                                f.name === file.name && f.size === file.size
                            );

                            if (!existingFile) {
                                // Add file to current files
                                currentFiles.items.add(file);
                            }
                        });

                        // Update file input
                        fileInput.files = currentFiles.files;

                        // Show previews
                        updateFilePreview();
                    }

                    function updateFilePreview() {
                        previewContainer.innerHTML = '';
                        const files = currentFiles.files;

                        if (files.length > 0) {
                            Array.from(files).forEach((file, index) => {
                                const fileDiv = document.createElement('div');
                                fileDiv.className = 'preview-item';
                                const isImage = file.type.startsWith('image/');
                                const isVideo = file.type.startsWith('video/');
                                const fileSize = (file.size / (1024 * 1024)).toFixed(2); // Convert to MB

                                let preview = '';
                                if (isImage) {
                                    preview = `
                                <div class="file-preview">
                                    <img src="${URL.createObjectURL(file)}" alt="Preview">
                                </div>
                            `;
                                } else if (isVideo) {
                                    preview = `
                                <div class="file-preview video">
                                    <i class="bi bi-play-circle-fill"></i>
                                </div>
                            `;
                                }

                                let icon = 'bi-file-earmark';
                                if (isImage) icon = 'bi-file-image';
                                if (isVideo) icon = 'bi-file-play';
                                if (file.name.endsWith('.pdf')) icon = 'bi-file-pdf';
                                if (file.name.endsWith('.doc') || file.name.endsWith('.docx')) icon =
                                    'bi-file-word';

                                fileDiv.innerHTML = `
                            ${preview}
                            <div class="file-info">
                                <div class="file-name">${file.name}</div>
                                <div class="file-size">${fileSize} MB</div>
                            </div>
                            <span class="remove-file" data-index="${index}">×</span>
                        `;
                                previewContainer.appendChild(fileDiv);
                            });

                            // Handle remove buttons
                            document.querySelectorAll('.remove-file').forEach(button => {
                                button.addEventListener('click', () => {
                                    const index = parseInt(button.dataset.index);
                                    const newFiles = new DataTransfer();

                                    Array.from(currentFiles.files)
                                        .filter((_, i) => i !== index)
                                        .forEach(file => newFiles.items.add(file));

                                    currentFiles = newFiles;
                                    fileInput.files = currentFiles.files;
                                    updateFilePreview();
                                });
                            });
                        }

                        // Form submission
                        document.getElementById('complaintForm').addEventListener('submit', function(e) {
                            const subject = document.getElementById('subject').value.trim();
                            const description = document.getElementById('description').value.trim();

                            if (!subject || !description) {
                                e.preventDefault();
                                alert('Please fill in all required fields');
                            }
                        });
                    });
    </script>
</body>

</html>
