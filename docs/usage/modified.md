Usage only modified configured response in controllers
======================================================

```php
namespace Acme\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController extends Controller
{
    /**
     * @var Request $request
     * @var int $id
     */
    public function showAction(Request $request, $id)
    {
        // cache becomes invalid if changing at least one of the pages, the catalog or the updated project
        $response = $this->get('cache_time_keeper')->getModifiedResponse(
            $request,
            [
                'AcmeDemoBundle:Page:'.$id, // handle the change only for this entity
                'AcmeDemoBundle:Catalog', // handle the change of any entities
            ],
            600, // cache liftime
            new JsonResponse(), // configure concrete response
        );

        // if the cache is valid, the next following code will not be executed

        // get entities
        $page = $this->getDoctrine()->getRepository('AcmeDemoBundle:Page')->find($id);
        $catalogs = $this->getDoctrine()->getRepository('AcmeDemoBundle:Catalog')->findAll();

        return $response->setData([
            'page' => $page,
            'catalogs' => $catalogs,
        ]);
    }
}
```
