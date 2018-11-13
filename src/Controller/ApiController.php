<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UnitConverter\Exception\NotSupportedConversionException;
use UnitConverter\Exception\NotSupportedUnitException;
use UnitConverter\Exception\QueryException;

class ApiController extends Controller
{
    /**
     * @Route("/api/convert", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the converted value"
     * )
     * @SWG\Parameter(
     *     name="query",
     *     in="query",
     *     type="string",
     *     description="The field used to order rewards"
     * )
     * @param Request $request
     * @return JsonResponse
     *
     * @throws \UnitConverter\Exception\NotSupportedConversionException
     * @throws \UnitConverter\Exception\NotSupportedUnitException
     * @throws \UnitConverter\Exception\QueryException
     */
    public function convertAction(Request $request)
    {
        $rawQuery = $request->query->get('query');

        try {
            $convertManager = $this->container->get('unit_converter.manager');
            $convertedValue = $convertManager->convert($rawQuery);

            return new JsonResponse([
                'status' => 'ok',
                'data' => [
                    'query' => $rawQuery,
                    'converted_value' => $convertedValue->getValue(),
                    'converted_unit' => $convertedValue->getUnit()->getName()
                ]
            ]);
        } catch (NotSupportedConversionException | NotSupportedUnitException | QueryException $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}