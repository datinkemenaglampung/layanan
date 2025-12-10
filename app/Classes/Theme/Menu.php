<?php

namespace App\Classes\Theme;

use App\Models\Permissions\MenuManager;
use App\Models\NavbarWeb;
use App\Models\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;


class Menu
{

  public static function sidebar()
  {
    $menuManager = new MenuManager();
    $roleId = isset(Auth::user()->role_id) ? Auth::user()->role_id : NULL;
    $menu_list = $menuManager->get_menu_role((isset(Auth::user()->role_id) ? Auth::user()->role_id : 0));
    $roots = [];
    foreach ($menu_list as $v) :
      $v->parent_id == 0 ? array_push($roots, $v->id) : array_push($roots, $v->parent_id);
    endforeach;
    $roots = array_unique($roots);
    $roots = MenuManager::whereIn('id', $roots)
      ->orderBy('sort', 'asc')
      ->get();
    return self::tree($roots, $menu_list, $roleId);
  }

  public static function tree($roots, $menu_list, $roleId, $parentId = 0, $endChild = 0)
  {
    $html = '';
    foreach ($roots as $v) :
      if ($v->type == 'module') {
        $html .= '<li class="' . ($v->path_url == request()->getPathInfo() ? 'active' : '') . '">
                     <a class="nav-link" href="' . $v->path_url . '">
                        <i class="' . ($v->icon ?? '') . '">
                        </i>
                        <span>' . $v->title . '</span>
                     </a></li>
               ';
      } elseif ($v->type == 'static') {
        $list_menu = $menu_list->where('parent_id', $v->id)->sortBy('sort');

        $get_path = $menu_list->where('path_url', request()->getPathInfo())->first();

        $html .= '<li class="dropdown ' . ($get_path !== null && $v->id == $get_path->parent_id ? 'active' : '') . '" >
                     <a class="nav-link has-dropdown" data-toggle="dropdown" href="#">
                        <i class="' . ($v->icon ?? '') . '">
                        </i>
                        <span>' . $v->title . '
                        </span>
                     </a>
                <ul class="dropdown-menu">
               ';


        foreach ($list_menu as $item) :
          $icon = isset($item->icon) ? '<i class="' . $item->icon . '"></i> ' : '<i class="far fa-circle nav-icon"></i>';
          $html .= '
            <li class="' . ($item->path_url == request()->getPathInfo() ? 'active' : '') . '">
                <a class="nav-link"
                    href="' . URL::to($item->path_url) . '">
                    ' . $icon . '
                    ' . $item->title . '
                </a>
            </li>
          ';
        endforeach;
        $html .= '
        </ul>
        </li>';
      } elseif ($v->type == 'header') {
        $html .= '<li class="menu-header">
                            ' . $v->title . '
                        </li>
               ';
      } else {
        $html .= '<li><hr class="hr-horizontal"></li>';
      }
    endforeach;
    return $html;
  }
}
