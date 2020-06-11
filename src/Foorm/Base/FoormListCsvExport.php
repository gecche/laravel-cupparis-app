<?php

namespace Gecche\Cupparis\App\Foorm\Base;

class FoormListCsvExport extends FoormList
{

    /**
     * Generates and returns the data list
     *
     * - Defines the relations of the model involved in the form
     * - Generates an initial builder
     * - Apply search filters if any
     * - Apply the desired list order
     * - Paginate results
     * - Apply last transformations to the list
     * - Format the result
     *
     */
    public function getFormData()
    {

        $this->getFormBuilder();

        $this->setAggregatesBuilder();

        return $this->formData;

    }


}
