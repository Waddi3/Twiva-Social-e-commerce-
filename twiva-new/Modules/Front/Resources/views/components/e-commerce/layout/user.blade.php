<x-front::e-commerce.layout.utilities.drawer class="flex flex-col justify-between items-start bg-gray-25 md:bg-white"
    name="user">
    <div class="w-full h-[calc(100%-118px)]">
        @if (request('account'))
            <section>
                <div class="flex justify-end">
                    <button @click="toggle">
                        <i class="w-5 h-5 text-gray-900" data-feather="x"></i>
                    </button>
                </div>
                <a href="{{ route('buyer.order.index', ['account' => request('account')]) }}"
                    class="p-4 flex">
                    <i data-feather="shopping-bag" class="user-nav-item-icon"></i>
                    <span class="user-nav-item-text ">My Orders</span>

                </a>
                <a href="{{ route('buyer.settings-page', ['account' => request('account')]) }}"
                    class="p-4 flex">
                    <i data-feather="settings" class="user-nav-item-icon"></i>
                    <span class="user-nav-item-text">Account Settings</span>
                </a>

                <form method="POST"
                    action="{{ route('buyer.logout', ['account' => request('account')]) }}"
                    class="p-4 flex">
                    @csrf
                    <button class="flex">
                        <i data-feather="log-in" class="user-nav-item-icon"></i>
                        <span class="user-nav-item-text ">Logout</span>
                    </button>
                </form>
            </section>
        @else
            <section>
                <div class="flex justify-end">
                    <button @click="toggle">
                        <i class="w-5 h-5 text-gray-900" data-feather="x"></i>
                    </button>
                </div>
                <a href="{{ route('e-commerce.buyer.order.index') }}"
                    class="p-4 flex">
                    <i data-feather="shopping-bag" class="user-nav-item-icon"></i>
                    <span class="user-nav-item-text ">My Orders</span>

                </a>
                <a href="{{ route('e-commerce.buyer.settings-page') }}"
                    class="p-4 flex">
                    <i data-feather="settings" class="user-nav-item-icon"></i>
                    <span class="user-nav-item-text">Account Settings</span>
                </a>

                <form method="POST"
                    action="{{ route('e-commerce.buyer.logout') }}"
                    class="p-4 flex">
                    @csrf
                    <button class="flex">
                        <i data-feather="log-in" class="user-nav-item-icon"></i>
                        <span class="user-nav-item-text ">Logout</span>
                    </button>
                </form>
            </section>
        @endif
    </div>
</x-front::e-commerce.layout.utilities.drawer>
