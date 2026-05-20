<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assistant - Laravel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.6/dist/purify.min.js"></script>
    <style>
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .markdown-content h1,
        .markdown-content h2,
        .markdown-content h3,
        .markdown-content h4,
        .markdown-content h5,
        .markdown-content h6 {
            @apply font-bold mt-4 mb-2 text-white;
        }

        .markdown-content h1 {
            @apply text-2xl;
        }

        .markdown-content h2 {
            @apply text-xl;
        }

        .markdown-content h3 {
            @apply text-lg;
        }

        .markdown-content p {
            @apply mb-3 text-gray-200;
        }

        .markdown-content ul,
        .markdown-content ol {
            @apply mb-3 ml-5;
        }

        .markdown-content li {
            @apply mb-2 text-gray-200;
        }

        .markdown-content code {
            @apply bg-gray-800 px-2 py-1 rounded text-amber-300 text-sm font-mono;
        }

        .markdown-content pre {
            @apply bg-gray-800 p-4 rounded mb-3 overflow-x-auto;
        }

        .markdown-content pre code {
            @apply bg-transparent px-0 py-0;
        }

        .markdown-content blockquote {
            @apply border-l-4 border-purple-500 pl-4 py-2 italic text-gray-300 mb-3;
        }

        .markdown-content strong {
            @apply font-bold text-white;
        }

        .markdown-content em {
            @apply italic;
        }

        .markdown-content a {
            @apply text-blue-400 hover:text-blue-300 underline;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div
            class="absolute top-20 left-20 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
        </div>
        <div
            class="absolute top-40 right-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute -bottom-8 left-1/2 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000">
        </div>
    </div>

    <div class="relative min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full mb-4 shadow-lg">
                    <i class="fas fa-brain text-white text-2xl"></i>
                </div>
                <h1 class="text-5xl font-bold text-white mb-3">Laravel AI Assistant</h1>
                <p class="text-xl text-purple-200">Ask anything about Laravel!</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl p-8 border border-white/20 mb-8">
                <div class="mb-6 p-4 bg-purple-500/20 rounded-lg border border-purple-500/30">
                    <div class="flex items-center justify-between">
                        <span class="text-purple-200"><i class="fas fa-fire-flame mr-2"></i>Respons quota for this session :</span>
                        <span class="text-2xl font-bold text-white" id="remainingCount">{{ $remainingResponses }}</span>
                        <span class="text-purple-200">/ {{ $maxResponses }}</span>
                    </div>
                </div>

                <div id="responsesContainer" class="mb-8 space-y-4 max-h-96 overflow-y-auto">
                    @if (isset($response))
                        <div
                            class="p-6 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 rounded-xl border border-emerald-500/30">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-check-circle text-emerald-400 mr-2 text-lg"></i>
                                <h3 class="text-lg font-semibold text-emerald-100">Response Example:</h3>
                            </div>
                            <div class="markdown-content text-white leading-relaxed">
                                {!! nl2br(e($response)) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <form id="promptForm" class="space-y-6">
                    @csrf
                    <div>
                        <label for="prompt"
                            class="block text-sm font-semibold text-purple-200 mb-3 uppercase tracking-wide">
                            <i class="fas fa-pencil-alt mr-2"></i>Your Question
                        </label>
                        <textarea id="prompt" name="prompt" rows="5"
                            class="w-full px-5 py-4 bg-white/5 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none transition duration-200"
                            placeholder="Ask anything about Laravel... For example: What is the difference between a Model and a Migration?" required></textarea>
                        <p id="promptError" class="mt-2 text-red-400 text-sm hidden flex items-center"><i
                                class="fas fa-exclamation-circle mr-1"></i><span id="promptErrorMessage"></span></p>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-xl transition duration-200 transform hover:scale-105 shadow-lg flex items-center justify-center"
                        disabled>
                        <i class="fas fa-wand-magic-sparkles mr-2"></i>
                        <span id="submitText">Generate Response AI</span>
                    </button>
                </form>

                <div id="errorDisplay"
                    class="hidden mt-6 bg-red-500/20 backdrop-blur-md border border-red-500/30 rounded-xl p-6">
                    <div class="flex items-start">
                        <i class="fas fa-triangle-exclamation text-red-400 text-xl mr-3 mt-1 flex-shrink-0"></i>
                        <div>
                            <h3 class="text-red-200 font-semibold mb-2">There is an error</h3>
                            <p class="text-red-100" id="errorMessage"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('promptForm');
        const promptInput = document.getElementById('prompt');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const responsesContainer = document.getElementById('responsesContainer');
        const errorDisplay = document.getElementById('errorDisplay');
        const errorMessage = document.getElementById('errorMessage');
        const promptError = document.getElementById('promptError');
        const promptErrorMessage = document.getElementById('promptErrorMessage');
        const remainingCount = document.getElementById('remainingCount');

        promptInput.addEventListener('input', function() {
            submitBtn.disabled = this.value.trim() === '';
        });

        submitBtn.disabled = promptInput.value.trim() === '';

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const prompt = promptInput.value.trim();

            if (!prompt) {
                promptErrorMessage.textContent = 'Your question cannot be empty';
                promptError.classList.remove('hidden');
                return;
            }

            promptError.classList.add('hidden');
            errorDisplay.classList.add('hidden');
            submitBtn.disabled = true;
            submitText.textContent = 'Loading...';

            try {
                const response = await fetch('{{ route('ai.generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({
                        prompt: prompt
                    })
                });

                const data = await response.json();

                if (data.success) {
                    const responseDiv = document.createElement('div');
                    responseDiv.className =
                        'p-6 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-xl border border-blue-500/30 animate-fade-in';

                    const markdownHtml = DOMPurify.sanitize(marked.parse(data.response));

                    responseDiv.innerHTML = `
                        <div class="flex items-center mb-3">
                            <i class="fas fa-sparkles text-blue-400 mr-2 text-lg"></i>
                            <h3 class="text-lg font-semibold text-blue-100">Respons AI</h3>
                        </div>
                        <div class="markdown-content text-white leading-relaxed">
                            ${markdownHtml}
                        </div>
                    `;

                    responsesContainer.insertBefore(responseDiv, responsesContainer.firstChild);

                    remainingCount.textContent = data.remainingResponses;

                    promptInput.value = '';
                    submitBtn.disabled = true;

                    if (data.remainingResponses === 0) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        promptInput.disabled = true;
                        submitText.textContent = 'Response Limit Reached';
                    }
                } else {
                    errorMessage.textContent = data.message || 'An unknown error occurred';
                    errorDisplay.classList.remove('hidden');

                    if (response.status === 429) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        promptInput.disabled = true;
                        submitText.textContent = 'Response Limit Reached';
                    }
                }
            } catch (error) {
                errorMessage.textContent = 'Failed to send request. Please check your internet connection.';
                errorDisplay.classList.remove('hidden');
                console.error(error);
            } finally {
                submitText.textContent = 'Generate Response AI';
                if (!submitBtn.disabled) {
                    submitBtn.disabled = promptInput.value.trim() === '';
                }
            }
        });
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</body>

</html>
