Base usage in controllers
=========================================

```php
namespace Acme\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    /**
     * @var Request $request
     * @var int $id
     */
    public function showAction(Request $request, $id)
    {
        // caching
        $response = new Response();

        // cache becomes invalid if changing at least one of the pages, the catalog or the updated project
        $response->setLastModified($this->get('cache_time_keeper')->getMax([
            'AcmeDemoBundle:Page:'.$id, // handle the change only for this entity
            'AcmeDemoBundle:Catalog', // handle the change of any entities
        ]));

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // get entities
        $page = $this->getDoctrine()->getRepository('AcmeDemoBundle:Page')->find($id);
        $catalogs = $this->getDoctrine()->getRepository('AcmeDemoBundle:Catalog')->findAll();

        return $this->render(
            'AcmeDemoBundle:Home:index.html.twig',
            [
                'page' => $page,
                'catalogs' => $catalogs,
            ],
            $response,
        );
    }
}
```
