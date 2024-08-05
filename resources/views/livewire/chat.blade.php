<div class="py-5">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white h-[40rem] overflow-hidden shadow-xl sm:rounded-lg">
            <!-- component -->
            <div class="flex h-[40rem] overflow-hidden">
                <!-- Sidebar -->
                <div class="w-1/4 bg-white border-r border-gray-300">
                    <!-- Sidebar Header -->
                    <header
                        class="p-4 border-b border-gray-300 flex justify-between items-center bg-indigo-600 text-white">
                        <h1 class="text-2xl font-semibold">Chat Box</h1>
                        <div class="relative">
                            <button id="menuButton" class="focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-100"
                                     viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path d="M2 10a2 2 0 012-2h12a2 2 0 012 2 2 2 0 01-2 2H4a2 2 0 01-2-2z"/>
                                </svg>
                            </button>
                        </div>
                    </header>

                    <!-- Contact List -->
                    <div class="overflow-y-auto h-screen p-3 mb-9 pb-20">
                        @foreach($users as $userr)
                            <div wire:key="{{ $userr->id }}" wire:click="selectUser({{ $userr }})"
                                 class="flex items-center mb-4 cursor-pointer hover:bg-gray-100 p-2 rounded-md">
                                <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
                                    <img src="https://placehold.co/200x/ffa8e4/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato"
                                         alt="User Avatar" class="w-12 h-12 rounded-full">
                                </div>
                                <div class="flex-1">
                                    <h2 class="text-lg font-semibold">{{ $userr->name }}</h2>
                                    @php
                                        $mostRecentMessage = \App\Models\Message::where(function ($q) use ($userr) {
                                            $q->where('sender_id', auth()->id())->where('receiver_id', $userr->id);
                                        })->orWhere(function ($q) use ($userr) {
                                            $q->where('sender_id', $userr->id)->where('receiver_id', auth()->id());
                                        })->orderBy('created_at', 'desc')->first();
                                    @endphp

                                    @if($mostRecentMessage)
                                        <p class="text-gray-600">{{ $mostRecentMessage->message }}</p>
                                    @else
                                        <p class="text-gray-600">No message</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Main Chat Area -->
                <div class="flex-1 relative ">
                    <!-- Chat Header -->
                    <header class="bg-indigo-600 p-4 text-white">
                        <h1 class="text-2xl font-semibold">{{ $user->name ?? 'Select a user' }}</h1>
                    </header>

                    <!-- Chat Messages -->
                    <div class="h-[40rem] overflow-y-auto p-2 pb-36">
                        @foreach($messages as $echoMessage)
                            @if($user->id !== $echoMessage['receiver_id'])
                                <!-- Incoming Message -->
                                <div class="flex mb-4 cursor-pointer">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center mr-2">
                                        <img src="https://placehold.co/200x/ffa8e4/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato"
                                             alt="User Avatar" class="w-8 h-8 rounded-full">
                                    </div>
                                    <div class="flex max-w-96 bg-gray-200 rounded-lg p-3 gap-3">
                                        <p class="text-gray-700">{{ $echoMessage['message'] }}</p>
                                    </div>
                                </div>
                            @else
                                <!-- Outgoing Message -->
                                <div class="flex justify-end mb-4 cursor-pointer">
                                    <div class="flex max-w-96 bg-indigo-500 text-white rounded-lg p-3 gap-3">
                                        <p>{{ $echoMessage['message'] }}</p>
                                    </div>
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center ml-2">
                                        <img src="https://placehold.co/200x/b7a8ff/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato"
                                             alt="My Avatar" class="w-8 h-8 rounded-full">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if($typing)
                            <div class="flex mb-4 cursor-pointer">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center mr-2">
                                    <img
                                        src="https://placehold.co/200x/ffa8e4/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato"
                                        alt="User Avatar" class="w-8 h-8 rounded-full">
                                </div>
                                <div class="chat-bubble">
                                    <div class="typing">
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Chat Input -->
                    <footer x-data
                            class="bg-white border-t border-gray-300 p-4 absolute bottom-0 left-0 right-0 w-full">
                        <div class="flex items-center">
                            <input wire:model="oneTimeMessage"
                                   type="text"
                                   id="oneTimeMessage"
                                   placeholder="Type a message..."
                                   class="w-full p-2 rounded-md border border-gray-400 focus:outline-none focus:border-blue-500">
                            <button wire:click="sendMessage"
                                    class="bg-indigo-500 text-white px-4 py-2 rounded-md ml-2">
                                Send
                            </button>
                        </div>
                        @error('message'){{ $message }}@enderror
                    </footer>
                </div>
            </div>
            <script>
                // JavaScript for showing/hiding the menu
                const menuButton = document.getElementById('menuButton');
                const menuDropdown = document.getElementById('menuDropdown');

                menuButton.addEventListener('click', () => {
                    if (menuDropdown.classList.contains('hidden')) {
                        menuDropdown.classList.remove('hidden');
                    } else {
                        menuDropdown.classList.add('hidden');
                    }
                });
            </script>
        </div>
    </div>
</div>
