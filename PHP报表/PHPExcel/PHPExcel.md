#PHPExcel
***
###什么是PHPExcel？
>PHPExcel 是用来操作Office Excel 文档的一个PHP类库，它基于微软的OpenXML标准和PHP语言。
>
>可以使用它来读取、写入不同格式的电子表格，如 Excel (BIFF) .xls, Excel 2007 (OfficeOpenXML) .xlsx, CSV, Libre/OpenOffice Calc .ods, Gnumeric, PDF, HTML等等。

###PHPExcel导出步骤
>1.新建一个excel表格：实例化PHPExcel类
>
>2.创建sheet(内置表)
>
>>createSheet()方法
>
>>setActiveSheetIndex方法
>
>>getActiveSheet方法

>3.填充数据
>>setCellValue()方法

>4.保存文件
>>PHPExcel_IOFactory::creayeWriter()方法
>
>>save()方法


###PHPExcel导出报表
>简单写入数据，导出excel文件

    public function index(){
      Vendor('PHPExcel.PHPExcel');

      //实例化PHPExcel对象,相当于新建一个excel表格,注意,不能少了\
      $objPHPExcel = new \PHPExcel();
      //获取当前活动sheet的操作对象
      $objSheet = $objPHPExcel->getActiveSheet();
      //给当前活动sheet设置名称
      $objSheet->setTitle("demo");
      //给当前sheet填充数据-mode1
      $objSheet->setCellValue("A1","姓名")->setCellValue("B1","分数");
      $objSheet->setCellValue("A2","黄斌")->setCellValue("B2","60");
      //直接加载数据块来填充数据-mode2
      //shortcoming1:如果数据量大的话不支持数据块存储,很容易产生内存不够的错误造成执行终端
      //shortcoming2:无法给单元格设定样式
      // $array = array(
      //   array(),
      //   array('','姓名','分数'),
      //   array('','黄斌','60'),
      //   array('','silence','60'),
      // );
      // $objSheet->fromArray($array);

      //按照指定格式生成excel文件,注意,不能少了\
      //生成xls后缀的文件-mode1
      // $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
      // $res = $objWriter->save($_SERVER['DOCUMENT_ROOT']."/data/excelDir"."/demo1.xls");
      //生成xls后缀的文件-mode2
      $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
      $res = $objWriter->save($_SERVER['DOCUMENT_ROOT']."/data/excelDir"."/demo1.xlsx");
    }

>数据库读取数据，浏览器导出excel文件

    /**
     * 获取用户表数据(输出至浏览器)
     * @param audit 1,审核通过;2,未通过
     * @return array    
     */
    public function browser_export($type, $filename){
      if ($type == "Excel5") {
        //告诉浏览器输出excel03文件
        header('Content-Type: application/vnd.ms-excel');
      }else{
        //告诉浏览器输出excel07文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      }
      //告诉浏览器将输出文件的名称
      header('Content-Disposition: attachment;filename="'.$filename.'"');
      header('Cache-Control: max-age=0');//禁止缓存

      // If you're serving to IE over SSL, then the following may be needed
      // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      // header ('Pragma: public'); // HTTP/1.0
    }

    /**
     * 导出EXCEL(用户表数据)
     * @param null
     * @return file
     */
    public function expUserExcel(){
      Vendor('PHPExcel.PHPExcel');

      $objPHPExcel = new \PHPExcel();
      //需要2个sheet表,因为默认会有一个sheet表,所以只需要创建一个
      for ($i=1; $i <=2 ; $i++) { 
        if ($i>1) {
          $objPHPExcel->createSheet();
        }
        $objPHPExcel->setActiveSheetIndex($i-1);//选取sheet,默认从0开始
        $objSheet = $objPHPExcel->getActiveSheet();//获取该sheet实例
        $data = $this->getUserList($i);//$i=1表示审核通过的数据,$i=2表示审核未通过的数据

        $i == 1 ? $objSheet->setTitle("审核通过") : $objSheet->setTitle("审核未通过");
        $objSheet->getColumnDimension('B')->setWidth(10);//设置列宽
        $objSheet->getColumnDimension('C')->setWidth(15);
        $objSheet->getColumnDimension('D')->setWidth(20);
        $objSheet->getColumnDimension('E')->setWidth(10);
        $objSheet->getColumnDimension('F')->setWidth(10);
        $objSheet
        ->setCellValue("A1","id")->setCellValue("B1","用户名")->setCellValue("C1","联系方式")
        ->setCellValue("D1","email")->setCellValue("E1","云主机定价")->setCellValue("F1","云磁盘定价");

        $j=2;//从第二行开始
        foreach ($data as $key => $val) {
          $objSheet
          ->setCellValue("A".$j, $val["user_id"])->setCellValue("B".$j, $val["user_name"])
          ->setCellValue("C".$j, $val["tel"])->setCellValue("D".$j, $val["email"])
          ->setCellValue("E".$j, $val["rate"])->setCellValue("F".$j, $val["disk_rate"]);

          $j++;
        }
      }
      $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
      // $objWriter->save($_SERVER['DOCUMENT_ROOT']."/data/excelDir"."/user.xlsx");
      $this->browser_export("Excel5", "browser_excel1.xlsx");
      $objWriter->save("php://output");
    }

###PHPExcel生成折线图报表
>1.只有Excel2007的格式才能生成图形报表
>
>2.图形报表实例都在PHPExcel_1.8.0_doc/Examples中

    /**
     * excel折线图图表
     */
    public function chartExcel(){
      Vendor('PHPExcel.PHPExcel');

      $objPHPExcel = new \PHPExcel();
      $objSheet = $objPHPExcel->getActiveSheet();

      $array=array(
          array("","一班","二班","三班"),
          array("不及格",20,30,40),
          array("良好",30,50,55),
          array("优秀",15,17,20)
      );//准备数据
      $objSheet->fromArray($array);//直接加载数组填充进单元格内

      /* PHPExcel的API文档在/PHPExcel_1.8.0_doc/Documentation/API/classes */
      //开始图表代码编写
      $labels=array(
        //取得绘制图表的标签
        //param1:数据类型,param2:WorkSheet为特有写法
        //param3:对应样式,一般为null,param4:取得数据的个数
        new \PHPExcel_Chart_DataSeriesValues('String','Worksheet!$B$1',null,1),//一班
        new \PHPExcel_Chart_DataSeriesValues('String','Worksheet!$C$1',null,1),//二班
        new \PHPExcel_Chart_DataSeriesValues('String','Worksheet!$D$1',null,1),//三班
      );

      $xLabels=array(
        //取得图表X轴的刻度,Y轴的数据PHPExcel会自动生成好
        new \PHPExcel_Chart_DataSeriesValues('String','Worksheet!$A$2:$A$4',null,3)
      );

      $datas=array(
        new \PHPExcel_Chart_DataSeriesValues('Number','Worksheet!$B$2:$B$4',null,3),//取一班的数据
        new \PHPExcel_Chart_DataSeriesValues('Number','Worksheet!$C$2:$C$4',null,3),//取二班的数据
        new \PHPExcel_Chart_DataSeriesValues('Number','Worksheet!$D$2:$D$4',null,3)//取三班的数据
      );//取得绘图所需的数据

      $series=array(
        new \PHPExcel_Chart_DataSeries(
          \PHPExcel_Chart_DataSeries::TYPE_LINECHART,//折线图
          \PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
          range(0,count($labels)-1),
          $labels,//图表标签
          $xLabels,//X轴数据
          $datas//图表数据
        )
      );//根据取得的东西做出一个图表的框架
      $layout=new \PHPExcel_Chart_Layout();
      $layout->setShowVal(true);//折线目标点显示数据注释
      $areas=new \PHPExcel_Chart_PlotArea($layout,$series);
      $legend=new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT,$layout,false);
      $title=new \PHPExcel_Chart_Title("高一学生成绩分布");//图表标题
      $ytitle=new \PHPExcel_Chart_Title("value(人数)");//Y轴注释
      $chart=new \PHPExcel_Chart(
        'line_chart',
        $title,
        $legend,
        $areas,
        true,
        false,
        null,
        $ytitle
      );//生成一个图标
      $chart->setTopLeftPosition("A7")->setBottomRightPosition("K25");//给定图表所在表格中的位置

      $objSheet->addChart($chart);//将chart添加到表格中

      $objWriter=\PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');//生成excel文件
      $objWriter->setIncludeCharts(true);//加载图表

      $this->browser_export("Excel2007", "chart_excel.xlsx");
      $objWriter->save("php://output");
    }

###PHPExcel导入步骤
>1.实例化excel读取对象
>
>2.加载excel文件
>
>3.读取excel文件


###PHPExcel导入报表存储数据

    /**
     * 导入Excel文件所有内容
     */
    public function exportAllExcel(){
      Vendor('PHPExcel.PHPExcel.IOFactory');

      $filename =  $_SERVER['DOCUMENT_ROOT']."/thinkTest/data/excelDir"."/demo2.xls";
      $objPHPExcel = \PHPExcel_IOFactory::load($filename);//加载文件

      // $sheetCount = $objPHPExcel->getSheetCount();//获取excel文件里有多少个sheet
      // for($i=0; $i<$sheetCount; $i++){
      //   $data = $objPHPExcel->getSheet($i)->toArray();//读取每个sheet里的数据 全部放入到数组中
      //   print_r($data);
      // }

      //PHPExcel自带的迭代器
      foreach($objPHPExcel->getWorksheetIterator() as $sheet){//循环取sheet
          foreach($sheet->getRowIterator() as $row){//逐行处理
              if($row->getRowIndex()<2){//控制行数输出,从第二行开始读取
                continue;
              }
              foreach($row->getCellIterator() as $cell){//逐列读取
                  $data=$cell->getValue();//获取单元格数据
                  echo $data." ";
              }
              echo '<br/>';
          }
          echo '<br/>';
      }
    }

###注意
>之前在线上环境发现无法导出excel，但是在测试环境是正常的，有可能是PHP环境模块的缺失导致的，有可能线上环境的PHPExcel类内部调用到特定的模块；对比之后发现线上业务环境缺失下面的模块，具体还不明确到底是缺少那个模块，全部添加后导出功能正常了：
>>xmlreader  
>>xmlwriter  
>>Core  
>>curl  
>>iconv  
>>ctype  