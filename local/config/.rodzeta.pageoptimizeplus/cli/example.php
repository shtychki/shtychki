<?php

namespace Rodzeta\Pageoptimizeplus;

function ActionExample($basePath, $options)
{
	require Action("list"); // if need using other commands

	WithBitrix(function () use ($basePath, $options)
	{
		//\CModule::IncludeModule("rodzeta.pageoptimizeplus");
		//var_dump($basePath, $options);

		$rsUser = \CUser::GetByID(1);
		$arUser = $rsUser->Fetch();
		var_dump($arUser["ID"]);

		ActionList($basePath, $options); // use other command
	}); // if need using bitrix api
}
