<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <!-- NesTable -->
    <link rel="stylesheet" href="https://datalampung.kemenag.go.id/web/assets/plugins/nestable/nestable.css">
    <title>Document</title>
</head>

<body>

    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>
                    <a href="https://flowbite.com" class="flex ms-2 md:me-24">
                        <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 me-3" alt="FlowBite Logo" />
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">APP</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <div>
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo">
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    neil.sims@flowbite.com
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Sign out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                            <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                            <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                        </svg>
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" aria-controls="dropdown-layouts" data-collapse-toggle="dropdown-layouts">
                        <svg class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        <span class="flex-1 ml-3 text-left whitespace-nowrap" sidebar-toggle-item>Layouts</span>
                        <svg sidebar-toggle-item class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <ul id="dropdown-layouts" class="hidden py-2 space-y-2">
                        <li>
                            <a href="" class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">Stacked</a>
                        </li>
                        <li>
                            <a href="" class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">Sidebar</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>

    <div class="p-4 sm:ml-64">
        <div class="p-4 mt-14">
            <div class="w-full bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-2">
                <div class="p-4 text-white font-bold rounded-ss-lg">About</div>
                <div id="defaultTabContent">
                    <div class="p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800">
                        <form class="mx-auto">
                            <div class="mb-5">
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                                <input type="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@flowbite.com" required />
                            </div>
                            <div class="mb-5">
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your password</label>
                                <input type="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            </div>
                            <div class="flex items-start mb-5">
                                <div class="flex items-center h-5">
                                    <input id="remember" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" required />
                                </div>
                                <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Remember me</label>
                            </div>
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Modal toggle -->
            <button data-modal-target="default-modal" data-modal-toggle="default-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Toggle modal
            </button>


            <button data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="mt-3 block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Toggle modal
            </button>



            <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-3">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Product name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Color
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Apple MacBook Pro 17"
                            </th>
                            <td class="px-6 py-4">
                                Silver
                            </td>
                            <td class="px-6 py-4">
                                Laptop
                            </td>
                            <td class="px-6 py-4">
                                $2999
                            </td>
                            <td class="px-6 py-4">
                                <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                            </td>
                        </tr>
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Microsoft Surface Pro
                            </th>
                            <td class="px-6 py-4">
                                White
                            </td>
                            <td class="px-6 py-4">
                                Laptop PC
                            </td>
                            <td class="px-6 py-4">
                                $1999
                            </td>
                            <td class="px-6 py-4">
                                <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                            </td>
                        </tr>
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Magic Mouse 2
                            </th>
                            <td class="px-6 py-4">
                                Black
                            </td>
                            <td class="px-6 py-4">
                                Accessories
                            </td>
                            <td class="px-6 py-4">
                                $99
                            </td>
                            <td class="px-6 py-4">
                                <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                            </td>
                        </tr>
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Google Pixel Phone
                            </th>
                            <td class="px-6 py-4">
                                Gray
                            </td>
                            <td class="px-6 py-4">
                                Phone
                            </td>
                            <td class="px-6 py-4">
                                $799
                            </td>
                            <td class="px-6 py-4">
                                <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="dd" id="menuList">
                <ol class="dd-list">
                    <li class="dd-item dd3-item" data-id="1">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd3-content">Dashboard</div>
                        <div class="dd3-actions">
                            <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/1/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete btn-default"
                                    data-id="1"><i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="dd-item dd3-item" data-id="12">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd3-content">Bab</div>
                        <div class="dd3-actions">
                            <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/12/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete btn-default"
                                    data-id="12"><i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="dd-item dd3-item" data-id="13">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd3-content">Table</div>
                        <div class="dd3-actions">
                            <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/13/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete btn-default"
                                    data-id="13"><i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="dd-item dd3-item" data-id="16">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd3-content">Satuan Kerja</div>
                        <div class="dd3-actions">
                            <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/16/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete btn-default"
                                    data-id="16"><i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="dd-item dd3-item" data-id="17">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd3-content">Buku</div>
                        <div class="dd3-actions">
                            <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/17/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete btn-default"
                                    data-id="17"><i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="dd-item dd3-item" data-id="18">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd3-content">Ormas</div>
                        <div class="dd3-actions">
                            <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/18/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete btn-default"
                                    data-id="18"><i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="dd-item dd3-item" data-id="19">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd3-content">KUA</div>
                        <div class="dd3-actions">
                            <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/19/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete btn-default"
                                    data-id="19"><i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="dd-item dd3-item" data-id="2">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd3-content">Setting</div>
                        <div class="dd3-actions">
                            <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">S</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/2/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-delete btn-default"
                                    data-id="2"><i class="fa fa-fw fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <ol class="dd-list">
                            <li class="dd-item dd3-item" data-id="8">
                                <div class="dd-handle dd3-handle"></div>
                                <div class="dd3-content">Users</div>
                                <div class="dd3-actions">
                                    <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/8/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-delete btn-default"
                                            data-id="8"><i class="fa fa-fw fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                            <li class="dd-item dd3-item" data-id="9">
                                <div class="dd-handle dd3-handle"></div>
                                <div class="dd3-content">Roles</div>
                                <div class="dd3-actions">
                                    <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/9/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-delete btn-default"
                                            data-id="9"><i class="fa fa-fw fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                            <li class="dd-item dd3-item" data-id="10">
                                <div class="dd-handle dd3-handle"></div>
                                <div class="dd3-content">Menu Manager</div>
                                <div class="dd3-actions">
                                    <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/10/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-delete btn-default"
                                            data-id="10"><i class="fa fa-fw fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                            <li class="dd-item dd3-item" data-id="11">
                                <div class="dd-handle dd3-handle"></div>
                                <div class="dd3-content">Web</div>
                                <div class="dd3-actions">
                                    <div class="btn-group"><a href="#" class="btn btn-sm font-size-14">M</a><a href="https://datalampung.kemenag.go.id/web/index.php/backend/menu-manager/11/edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-delete btn-default"
                                            data-id="11"><i class="fa fa-fw fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </li>
                </ol>
            </div>

        </div>


    </div>


    <!-- Main modal -->
    <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Terms of Service
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        With less than a month to go before the European Union enacts new consumer privacy laws for its citizens, companies around the world are updating their terms of service agreements to comply.
                    </p>
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        The European Union’s General Data Protection Regulation (G.D.P.R.) goes into effect on May 25 and is meant to ensure a common set of data rights in the European Union. It requires organizations to notify users as soon as possible of high-risk data breaches that could personally affect them.
                    </p>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="default-modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
                    <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
                </div>
            </div>
        </div>
    </div>


    <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this product?</h3>
                    <button data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Yes, I'm sure
                    </button>
                    <button data-modal-hide="popup-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No, cancel</button>
                </div>
            </div>
        </div>
    </div>

    @yield('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <!-- jQuery -->
    <script src="https://datalampung.kemenag.go.id/web/assets/plugins/jquery/jquery.min.js"></script>
    <!-- Nestable -->
    <script src="https://datalampung.kemenag.go.id/web/assets/plugins/nestable/nestable.js"></script>
    <script>
        $(document).ready(function() {
            let radioCreate = document.querySelectorAll('input[name="type"]');

            $('#menuList').nestable({
                maxDepth: 2
            }).on('change', function() {
                let json_values = window.JSON.stringify($(this).nestable('serialize'));
                $("#output").val(json_values);
                $("#changeHierarchy [type='submit']").fadeIn();
            }).nestable('collapseAll');
        });
    </script>


</body>

</html>