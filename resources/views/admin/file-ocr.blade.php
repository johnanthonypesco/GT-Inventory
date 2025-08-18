<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>OCR Archives</title>
    <style>
        .file-item, .folder-item {
            user-select: none; transition: transform 0.1s ease-in-out, box-shadow 0.1s ease-in-out;
        }
        .file-item:hover, .folder-item:hover {
            transform: scale(1.03); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="flex flex-col md:flex-row m-0 p-0">
    
    <x-admin.navbar />

    <main class="md:w-full h-full lg:ml-[15%] ml-0 px-4">
        <x-admin.header title="OCR Archives" icon="fa-solid fa-folder-open"/>
        
        <div class="bg-white rounded-lg shadow p-6 mt-4">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button id="back-button" class="hidden px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Back
                    </button>
                    <h2 id="current-path" class="text-xl font-semibold text-gray-600">All Folders</h2>
                </div>
                <a href="{{ route('admin.inventory') }}" class="flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Inventory
                </a>
            </div>

            <div id="file-container">
                <div>
                    <h3 class="text-lg font-bold text-gray-700 mb-3 border-b pb-2">OCR Scanned (Original Copy)</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                        @forelse ($imageDirectories as $dir)
                            @php
                                $folderName = str_replace('receipts/', '', $dir);
                            @endphp
                            <div class="folder-item flex flex-col items-center justify-center p-4 border rounded-lg shadow-sm cursor-pointer" data-path="{{ $dir }}" data-type="image">
                                <i class="fa-solid fa-folder text-5xl text-yellow-500 mb-2"></i>
                                <span class="text-sm font-medium text-center text-gray-700">{{ $folderName }}</span>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-5 text-gray-500">
                                <p>No scanned receipt folders found yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-700 mb-3 border-b pb-2">DOCX Copy</h3>
                     <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                        @forelse ($docxDirectories as $dir)
                            @php
                                // We need to remove the base path to show a clean name
                                $folderName = str_replace('receipts/docs_ocr_copy/', '', $dir);
                            @endphp
                            <div class="folder-item flex flex-col items-center justify-center p-4 border rounded-lg shadow-sm cursor-pointer" data-path="{{ $dir }}" data-type="docx">
                                <i class="fa-solid fa-folder text-5xl text-blue-500 mb-2"></i>
                                <span class="text-sm font-medium text-center text-gray-700">{{ $folderName }}</span>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-5 text-gray-500">
                                <p>No DOCX copy folders found yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="image-modal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <button id="modal-close" class="absolute top-4 right-6 text-white text-5xl font-bold hover:text-gray-300">&times;</button>
        <img id="modal-image" src="" alt="Scanned Receipt" class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg">
    </div>

    <x-loader />

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileContainer = document.getElementById('file-container');
    const backButton = document.getElementById('back-button');
    const currentPathSpan = document.getElementById('current-path');
    const imageModal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');
    const modalClose = document.getElementById('modal-close');
    const loader = document.getElementById('loader');

    const initialFoldersHTML = fileContainer.innerHTML;

    // --- Event Listeners ---
    fileContainer.addEventListener('click', function(e) {
        const folder = e.target.closest('.folder-item');
        if (folder) {
            const path = folder.dataset.path;
            const type = folder.dataset.type; // Get the type (image or docx)
            loadFolderContents(path, type); // Pass type to the function
        }
    });

    // We only need the dblclick listener for images
    fileContainer.addEventListener('dblclick', function(e) {
        const imageItem = e.target.closest('.file-item[data-type="image"]');
        if (imageItem) {
            const imageUrl = imageItem.dataset.url;
            showImageModal(imageUrl);
        }
    });

    backButton.addEventListener('click', function() {
        showRootFolders();
    });

    modalClose.addEventListener('click', hideImageModal);
    imageModal.addEventListener('click', (e) => (e.target === imageModal) && hideImageModal());

    // --- Functions ---
    function showLoader() { loader.style.display = 'flex'; }
    function hideLoader() { loader.style.display = 'none'; }
    
    function loadFolderContents(path, type) { // Function now accepts 'type'
        showLoader();
        // Add the type parameter to the URL
        const url = `{{ route('admin.file-ocr.contents') }}?path=${encodeURIComponent(path)}&type=${type}`;

        fetch(url)
            .then(response => response.ok ? response.json() : Promise.reject('Network response was not ok'))
            .then(data => {
                const gridContainer = document.createElement('div');
                gridContainer.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4';

                if (data.files && data.files.length > 0) {
                    data.files.forEach(fileUrl => {
                        const fileName = fileUrl.split('/').pop();
                        let fileElement;

                        if (type === 'image') {
                            // Render image item (double-clickable)
                            fileElement = document.createElement('div');
                            fileElement.className = 'file-item flex flex-col items-center justify-center p-2 border rounded-lg shadow-sm cursor-pointer';
                            fileElement.dataset.url = fileUrl;
                            fileElement.dataset.type = 'image';
                            fileElement.innerHTML = `
                                <img src="${fileUrl}" alt="${fileName}" class="w-full h-24 object-cover rounded-md mb-2">
                                <span class="text-xs text-center break-all text-gray-600">${fileName}</span>
                            `;
                        } else if (type === 'docx') {
                            // Render DOCX item (clickable link)
                            fileElement = document.createElement('a');
                            fileElement.href = fileUrl;
                            fileElement.target = '_blank'; // Open in new tab
                            fileElement.className = 'file-item flex flex-col items-center justify-center p-4 border rounded-lg shadow-sm';
                            fileElement.innerHTML = `
                                <i class="fa-solid fa-file-word text-5xl text-blue-700 mb-2"></i>
                                <span class="text-xs text-center break-all text-gray-600">${fileName}</span>
                            `;
                        }
                        
                        if(fileElement) gridContainer.appendChild(fileElement);
                    });
                } else {
                    gridContainer.innerHTML = `<div class="col-span-full text-center py-10 text-gray-500"><p>This folder is empty.</p></div>`;
                }
                
                fileContainer.innerHTML = ''; // Clear the main container
                fileContainer.appendChild(gridContainer); // Append the new grid

                let folderDisplayName = path.replace('receipts/docs_ocr_copy/', '').replace('receipts/', '');
                currentPathSpan.textContent = `Folder: ${folderDisplayName}`;
                backButton.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching folder contents:', error);
                fileContainer.innerHTML = `<div class="col-span-full text-center py-10 text-red-500"><p>Failed to load folder contents.</p></div>`;
            })
            .finally(() => {
                hideLoader();
            });
    }

    function showRootFolders() {
        fileContainer.innerHTML = initialFoldersHTML;
        currentPathSpan.textContent = 'All Folders';
        backButton.classList.add('hidden');
    }

    function showImageModal(url) {
        modalImage.src = url;
        imageModal.classList.remove('hidden');
        imageModal.classList.add('flex');
    }

    function hideImageModal() {
        imageModal.classList.remove('flex');
        imageModal.classList.add('hidden');
        modalImage.src = '';
    }
});
</script>

</body>
</html>